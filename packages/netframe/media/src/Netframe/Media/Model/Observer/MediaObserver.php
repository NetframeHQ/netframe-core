<?php

namespace Netframe\Media\Model\Observer;

use Netframe\Media\MediaManagerInterface;
use Netframe\Media\Model\Media;

/**
 * Observe a Media.
 */
class MediaObserver
{
    private $mediaManager;

    public function __construct(MediaManagerInterface $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    /**
     * Delete the file associated to the media.
     *
     * @param Media $media
     */
    public function deleting(Media $media)
    {
        $this->mediaManager->deleteMediaFile($media);
    }
}
