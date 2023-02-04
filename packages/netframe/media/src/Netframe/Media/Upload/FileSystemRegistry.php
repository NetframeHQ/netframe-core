<?php

namespace Netframe\Media\Upload;

/**
 * Holds the filesystem corresponding to each media type.
 */
class FileSystemRegistry
{
    private $filesystems = array();

    /**
     * Add a file system.
     *
     * @param FileSystemInterface $filesystem The file system
     * @param string              $mediaType  A Media::TYPE_* constant
     */
    public function addFileSystem(FileSystemInterface $filesystem, $mediaType)
    {
        $this->filesystems[$mediaType] = $filesystem;
    }

    /**
     * Get the filesystem for the given media type.
     *
     * @param integer $mediaType A Media::TYPE_* constant
     *
     * @return FilesystemInterface
     *
     * @throws \RuntimeException
     */
    public function getFileSystem($mediaType)
    {
        if (isset($this->filesystems[$mediaType])) {
            return $this->filesystems[$mediaType];
        }

        throw new \RuntimeException(sprintf('The filesystem for media type "%s" is not registered', $mediaType));
    }
}
