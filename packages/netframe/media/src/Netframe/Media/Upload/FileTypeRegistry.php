<?php

namespace Netframe\Media\Upload;

/**
 * Holds all configured file types.
 */
class FileTypeRegistry
{
    /**
     * @var FileType[]
     */
    private $fileTypes = array();

    /**
     * Add a file type to the registry.
     *
     * @param FileType $fileType
     */
    public function addFileType(FileType $fileType)
    {
        if (isset($this->fileTypes[$fileType->getExtension()])) {
            $this->fileTypes[$fileType->getExtension()][] = $fileType;
        } else {
            $this->fileTypes[$fileType->getExtension()] = array($fileType);
        }
    }

    /**
     * Get the file type from an extension and mime type.
     *
     * @param string $extension
     * @param string $mimeType
     *
     * @return FileType
     *
     * @throws UnsupportedFileTypeException
     */
    public function getFileType($extension, $mimeType, $mimeTypeClient)
    {
        if (isset($this->fileTypes[$extension])) {
            foreach ($this->fileTypes[$extension] as $fileType) {
                if ($fileType->getMimeType() === $mimeType || $fileType->getMimeType() === $mimeTypeClient) {
                    return $fileType;
                }
            }
        }

        if ($mimeType == 'application/octet-stream' && $mimeTypeClient == 'application/octet-stream') {
            return $this->fileTypes[$extension][0];
        }

        throw new UnsupportedFileTypeException(sprintf(
            'The file type with extension "%s" and mime type "%s" is not supported',
            $extension,
            $mimeType
        ));
    }
}
