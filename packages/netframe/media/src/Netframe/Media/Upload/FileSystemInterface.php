<?php

namespace Netframe\Media\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Represents a file system.
 */
interface FileSystemInterface
{
    /**
     * Moves the file.
     *
     * @param UploadedFile $uploadedFile
     * @param string       $fileName
     *
     * @return \Symfony\Component\HttpFoundation\File\File The moved file
     */
    public function move(UploadedFile $uploadedFile, $fileName = null);

    /**
     * Deletes the file.
     *
     * @param string $fileName
     */
    public function delete($fileName);
}
