<?php

namespace Netframe\Media\Tests\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Netframe\Media\Model\Media;
use Netframe\Media\Upload\FileType;
use Netframe\Media\Upload\FileTypeRegistry;

class FileTypeRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFileType()
    {
        $registry = new FileTypeRegistry();
        $jpgFileType = new FileType(Media::TYPE_IMAGE, 'jpg', 'image/jpeg');
        $registry->addFileType($jpgFileType);
        $registry->addFileType(new FileType(Media::TYPE_IMAGE, 'jpeg', 'image/jpeg'));
        $fileType = $registry->getFileType('jpg', 'image/jpeg');

        $this->assertSame($jpgFileType, $fileType);
    }

    /**
     * @expectedException \Netframe\Media\Upload\UnsupportedFileTypeException
     */
    public function testGetFileTypeNotFound()
    {
        $registry = new FileTypeRegistry();
        $registry->getFileType('foo', 'bar');
    }
}
