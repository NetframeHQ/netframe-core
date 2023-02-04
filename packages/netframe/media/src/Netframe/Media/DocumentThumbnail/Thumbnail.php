<?php

namespace Netframe\Media\DocumentThumbnail;

use Netframe\Media\Model\Media;

class Thumbnail
{
    private $feed_path;
    private $thumb_path;

    public function __construct(string $thumb_path, string $feed_path = null)
    {
        $this->thumb_path = $thumb_path;
        $this->feed_path = $feed_path;
    }

    public function applyTo(Media $media): Media
    {
        $media->thumb_path = $this->thumb_path;
        $media->feed_path = $this->feed_path;
        $media->encoded = 1;

        return $media;
    }
}
