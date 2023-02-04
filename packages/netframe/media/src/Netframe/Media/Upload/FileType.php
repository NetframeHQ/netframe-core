<?php

namespace Netframe\Media\Upload;

/**
 * Represents a file type.
 */
class FileType
{
    private $mediaType;
    private $extension;
    private $mimeType;

    /**
     * Constructor.
     *
     * @param integer $mediaType The media type Media::TYPE_*
     * @param string  $extension The file extension
     * @param string  $mimeType  The mime type
     */
    public function __construct($mediaType, $extension, $mimeType)
    {
        $this->mediaType = $mediaType;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
    }

    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Get the extension.
     *
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Get the mime type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}
