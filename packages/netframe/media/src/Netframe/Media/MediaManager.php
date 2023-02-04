<?php

namespace Netframe\Media;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Netframe\Media\Import\ImporterRegistry;
use Netframe\Media\Import\UnsupportedUrlException;
use Netframe\Media\Model\Media;
use Netframe\Media\Upload\FileKeyGeneratorInterface;
use Netframe\Media\Upload\FileSystemRegistry;
use Netframe\Media\Upload\FileTypeRegistry;

/**
 * Manages medias.
 */
class MediaManager implements MediaManagerInterface
{
    private $importerRegistry;
    private $fileTypeRegistry;
    private $fileSystemRegistry;
    private $fileKeyGenerator;

    public function __construct(
        ImporterRegistry $importerRegistry,
        FileTypeRegistry $fileTypeRegistry,
        FileSystemRegistry $fileSystemRegistry,
        FileKeyGeneratorInterface $fileKeyGenerator
    ) {
        $this->importerRegistry = $importerRegistry;
        $this->fileTypeRegistry = $fileTypeRegistry;
        $this->fileSystemRegistry = $fileSystemRegistry;
        $this->fileKeyGenerator = $fileKeyGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function import($url, Media $media = null)
    {
        $importers = $this->importerRegistry->getImporters();

        foreach ($importers as $importer) {
            if (null !== $importInfos = $importer->parseId($url)) {
                if (null === $media) {
                    $media = new Media();
                }

                $media->name = $importInfos->name;
                $media->type = $importInfos->file_type;
                $media->file_name = $importInfos->file_name;
                $media->file_path = $importInfos->url;
                $media->thumb_path = $importInfos->thumb;
                $media->date = new \DateTime();
                $media->platform = $importer->getPlatform();
                $media->encoded = 1;

                $media->save();

                \Event::dispatch('media.import', array($media));

                return $media;
            }
        }

        throw new UnsupportedUrlException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getImporters()
    {
        return $this->importerRegistry->getImporters();
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFile $file, Media $media = null)
    {
        // $mimeTypeServer = $file->getMimeType();
        $mimeTypeClient = $file->getClientMimeType();
        $mimeType = $file->getMimeType();

        $extension = strtolower($file->getClientOriginalExtension());

        $fileType = $this->fileTypeRegistry->getFileType($extension, $mimeType, $mimeTypeClient);
        $fileName = $this->fileKeyGenerator->generate($file) . '.' . $extension;
        $mediaType = $fileType->getMediaType();
        $fileSystem = $this->fileSystemRegistry->getFileSystem($mediaType);

        $movedFile = $fileSystem->move($file, $fileName);

        if (null === $media) {
            $media = new Media();
        }

        $media->name = $file->getClientOriginalName();
        $media->type = $mediaType;
        $media->file_name = $fileName;

        /*
        * reactivate when separate instances folders
        if(session()->has('instanceId') && session('instanceId') != null){
            $pathToDir = config('media.file_systems.'.$mediaType.'.path').'/'.session('instanceId');
        }
        else{
            $pathToDir = config('media.file_systems.'.$mediaType.'.path');
        }
        */
        $pathToDir = config('media.file_systems.'.$mediaType.'.path');
        if (!file_exists($pathToDir)) {
            $result = \File::makeDirectory($pathToDir, 0775, true);
        }

        $media->file_path = $pathToDir.'/'.$fileName;
        $media->date = new \DateTime();
        $media->platform = 'local';
        $media->mime_type = $fileType->getMimeType();

        //save filesize
        $filesize = \File::size($media->file_path);
        $media->file_size = $filesize;

        $mediasTypeToEncode = [Media::TYPE_AUDIO, Media::TYPE_VIDEO, Media::TYPE_DOCUMENT];
        $media->encoded = (int) !in_array($mediaType, $mediasTypeToEncode);

        $media->save();

        //load event
        \Event::dispatch('media.upload', array($media));

        return $media;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMediaFile(Media $media)
    {
        if ($media->platform === 'local') {
            $fileSystem = $this->getMediaFileSystem($media);
            $fileSystem->delete($media->file_name);
        }
    }

    /**
     * Get the filesystem associated to this media.
     *
     * @param Media $media
     *
     * @return \Netframe\Media\Upload\FileSystemInterface
     */
    private function getMediaFileSystem(Media $media)
    {
        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
        $fileType = $this->fileTypeRegistry->getFileType($extension, $media->mime_type);
        $mediaType = $fileType->getMediaType();

        return $this->fileSystemRegistry->getFileSystem($mediaType);
    }
}
