<?php

namespace Netframe\Media;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Netframe\Media\Model\Media;

/**
 * Public interface of the media package.
 */
interface MediaManagerInterface
{
    /**
     * Imports a media from the url.
     *
     * @param string $url
     * @param Media  $media
     *
     * @return \Netframe\Media\Model\Media The created media
     */
    public function import($url, Media $media = null);

    /**
     * Gets the importers.
     *
     * @return \Netframe\Media\Import\ImporterInterface[]
     */
    public function getImporters();

    /**
     * Uploads the file.
     *
     * @param UploadedFile $file
     * @param Media        $media
     *
     * @return \Netframe\Media\Model\Media The created media
     */
    public function upload(UploadedFile $file, Media $media = null);

    /**
     * Deletes the file associated to the media.
     *
     * @param Media $media
     */
    public function deleteMediaFile(Media $media);
}
