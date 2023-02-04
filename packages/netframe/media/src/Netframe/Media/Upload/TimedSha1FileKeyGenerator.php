<?php

namespace Netframe\Media\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Generate a file name using the current time.
 */
class TimedSha1FileKeyGenerator implements FileKeyGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(UploadedFile $file)
    {
        return sha1($file->getClientOriginalName() . microtime());
    }
}
