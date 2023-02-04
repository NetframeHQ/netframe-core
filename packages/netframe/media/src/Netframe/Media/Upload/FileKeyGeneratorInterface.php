<?php

namespace Netframe\Media\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Generates a unique key for a given file.
 */
interface FileKeyGeneratorInterface
{
    /**
     * Generates a unique key for this file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    public function generate(UploadedFile $file);
}
