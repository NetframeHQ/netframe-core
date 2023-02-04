<?php

namespace Netframe\Media\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Local file system implementation.
 */
class LocalFileSystem implements FileSystemInterface
{
    private $path;

    /**
     * Constructor.
     *
     * @param string $path The path where to upload files
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function move(UploadedFile $uploadedFile, $name = null)
    {
        /*
        * reactivate when separate instances folders
        if($sessionId != null){
            $finalPath = $this->path.'/'.$sessionId;
        }
        else{
            $finalPath = $this->path;
        }
        */

        $finalPath = $this->path;
        return $uploadedFile->move($finalPath, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($fileName)
    {
        @unlink($this->path . '/' . $fileName);
    }
}
