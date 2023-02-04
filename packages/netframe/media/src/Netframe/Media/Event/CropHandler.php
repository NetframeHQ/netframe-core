<?php

namespace Netframe\Media\Event;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Image\Metadata\ExifMetadataReader;
use Netframe\Media\Model\Media;

class CropHandler
{
    const THUMB_PREFIX = 'thumbs-';
    const FEED_PREFIX = 'feed-';
    const MAX_IMAGE_DIMENSION = 800;
    const MAX_FEED_DIMENSION = 400;
    const THUMB_DIMENSION = 300;

    private $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    public function handle(Media $media)
    {
        if ($media->platform != 'local') {
            $this->createThumbnailFromImport($media);
        } else {
            if (Media::TYPE_IMAGE !== $media->type) {
                return;
            }

            $this->cropMainImage($media);
            $this->createFeedImage($media);
            $this->createThumbnail($media);
        }
    }

    private function cropMainImage(Media $media)
    {
        $imagePath = $media->file_path;
        $image = $this->imagine
            ->setMetadataReader(new ExifMetadataReader())
            ->open($imagePath);

        $metaData = $image->metadata();
        if (isset($metaData['ifd0.Orientation'])) {
            switch ($metaData['ifd0.Orientation']) {
                case 3:
                    $image->rotate(180);
                    break;
                case 6:
                    $image->rotate(90);
                    break;
                case 8:
                    $image->rotate(-90);
                    break;
                default:
                    break;
            }
        }

        $image->strip();

        $box = $image->getSize();

        if ($box->getWidth() <= $box->getHeight() && $box->getHeight() > 800) {
            $ratio = $box->getWidth() / $box->getHeight();
            $box = new Box(self::MAX_IMAGE_DIMENSION * $ratio, self::MAX_IMAGE_DIMENSION);
        } elseif ($box->getWidth() > 800) {
            $ratio = $box->getHeight() / $box->getWidth();
            $box = new Box(self::MAX_IMAGE_DIMENSION, self::MAX_IMAGE_DIMENSION * $ratio);
        } else {
            return;
        }

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 90,
        );

        $image->resize($box);
        $image->save($imagePath, $options);

        $sizesCropped = $image->getSize();
        $media->feed_path = $imagePath;
        $media->feed_width = $sizesCropped->getWidth();
        $media->feed_height = $sizesCropped->getHeight();
        $media->save();

        //save image size
        $filesize = \File::size($imagePath);
        $media->file_size = $filesize;
        $media->save();
    }

    private function createFeedImage(Media $media)
    {
        $imagePath = $media->file_path;
        $feedPath = str_replace($media->file_name, self::FEED_PREFIX.$media->file_name, $media->file_path);

        $image = $this->imagine
        ->setMetadataReader(new ExifMetadataReader())
        ->open($imagePath);


        $box = $image->getSize();

        if ($box->getWidth() <= $box->getHeight() && $box->getHeight() > 400) {
            $ratio = $box->getWidth() / $box->getHeight();
            $box = new Box(self::MAX_FEED_DIMENSION * $ratio, self::MAX_FEED_DIMENSION);
        } elseif ($box->getWidth() > 400) {
            $ratio = $box->getHeight() / $box->getWidth();
            $box = new Box(self::MAX_FEED_DIMENSION, self::MAX_FEED_DIMENSION * $ratio);
        } else {
            $media->feed_width = $box->getWidth();
            $media->feed_height = $box->getHeight();
            $media->save();
            return;
        }

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 90,
        );

        $image->resize($box);
        $image->save($feedPath, $options);
        $sizesCropped = $image->getSize();
        $media->feed_path = $feedPath;
        $media->feed_width = $sizesCropped->getWidth();
        $media->feed_height = $sizesCropped->getHeight();
        $media->save();

        //save image size
        $filesize = \File::size($feedPath);
        $media->file_size = $media->file_size + $filesize;
        $media->save();
    }

    private function createThumbnail(Media $media)
    {
        $imagePath = $media->file_path;
        $thumbPath = str_replace($media->file_name, self::THUMB_PREFIX.$media->file_name, $media->file_path);

        // Resize image
        $image = $this->imagine
            ->setMetadataReader(new ExifMetadataReader())
            ->open($imagePath);

        $box = $image->getSize();

        if ($box->getWidth() <= $box->getHeight()) {
            $ratio = $box->getWidth() / $box->getHeight();
            $box = new Box(self::THUMB_DIMENSION, self::THUMB_DIMENSION / $ratio);
        } else {
            $ratio = $box->getHeight() / $box->getWidth();
            $box = new Box(self::THUMB_DIMENSION / $ratio, self::THUMB_DIMENSION);
        }

        $image->resize($box);

        // Crop from the center
        $resizedBox = $image->getSize();
        $center = new Center($resizedBox);
        $start = new Point(
            $center->getX() - self::THUMB_DIMENSION / 2,
            $center->getY() - self::THUMB_DIMENSION / 2
        );

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 90,
        );

        $image->crop($start, new Box(self::THUMB_DIMENSION, self::THUMB_DIMENSION));
        $image->save($thumbPath, $options);
        $media->thumb_path = $thumbPath;
        $media->save();

        //save image size
        $filesize = \File::size($thumbPath);
        $media->file_size = $media->file_size + $filesize;
        $media->save();
    }

    private function createThumbnailFromImport(Media $media)
    {
        $tmpDir = config('media.tmp_storage');
        $imagePath = $tmpDir.'/'.$media->thumb_path;

        $thumbPath = config('media.file_systems.0.path').'/'.$media->thumb_path;

        // Resize image
        $image = $image = $this->imagine
        ->setMetadataReader(new ExifMetadataReader())
        ->open($imagePath);

        $box = $image->getSize();

        if ($box->getWidth() <= $box->getHeight()) {
            $ratio = $box->getWidth() / $box->getHeight();
            $box = new Box(self::THUMB_DIMENSION, self::THUMB_DIMENSION / $ratio);
        } else {
            $ratio = $box->getHeight() / $box->getWidth();
            $box = new Box(self::THUMB_DIMENSION / $ratio, self::THUMB_DIMENSION);
        }

        $image->resize($box);

        // Crop from the center
        $resizedBox = $image->getSize();
        $center = new Center($resizedBox);
        $start = new Point(
            $center->getX() - self::THUMB_DIMENSION / 2,
            $center->getY() - self::THUMB_DIMENSION / 2
        );

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 90,
        );

        $image->crop($start, new Box(self::THUMB_DIMENSION, self::THUMB_DIMENSION));
        $image->save($thumbPath, $options);
        $media->thumb_path = $thumbPath;
        $media->save();
    }
}
