<?php

namespace Netframe\Media\Tests;

use Netframe\Media\Import\DailymotionImporter;
use Netframe\Media\Import\ImporterRegistry;
use Netframe\Media\Import\YoutubeImporter;
use Netframe\Media\MediaManager;
use Netframe\Media\Model\Media;
use Netframe\Media\Upload\FileSystemRegistry;
use Netframe\Media\Upload\FileType;
use Netframe\Media\Upload\FileTypeRegistry;

class MediaManagerTest extends TestCase
{
    public function testImport()
    {
        $registry = new ImporterRegistry();
        $registry->addImporter(new YoutubeImporter());
        $registry->addImporter(new DailymotionImporter());
        $manager = new MediaManager(
            $registry,
            $this->getMockBuilder('Netframe\Media\Upload\FileTypeRegistry')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Netframe\Media\Upload\FileSystemRegistry')->disableOriginalConstructor()->getMock(),
            $this->getMock('Netframe\Media\Upload\FileKeyGeneratorInterface')
        );

        $youtubeMedia = $manager->import('http://www.youtube.com/watch?v=iwSOZDZvmWA');
        $this->assertEquals('http://www.youtube.com/watch?v=iwSOZDZvmWA', $youtubeMedia->name);
        $this->assertEquals(Media::TYPE_VIDEO, $youtubeMedia->type);
        $this->assertEquals('iwSOZDZvmWA', $youtubeMedia->file_name);
        $this->assertInstanceOf('\DateTime', $youtubeMedia->date);
        $this->assertEquals('youtube', $youtubeMedia->platform);

        $dailymotionMedia = $manager->import(
            'http://www.dailymotion.com/video/x2ayv94_martin-fourcade-peaufine-son-retour-a-la-competition_sport'
        );
        $this->assertEquals(
            'http://www.dailymotion.com/video/x2ayv94_martin-fourcade-peaufine-son-retour-a-la-competition_sport',
            $dailymotionMedia->name
        );
        $this->assertEquals(Media::TYPE_VIDEO, $dailymotionMedia->type);
        $this->assertEquals('x2ayv94', $dailymotionMedia->file_name);
        $this->assertInstanceOf('\DateTime', $dailymotionMedia->date);
        $this->assertEquals('dailymotion', $dailymotionMedia->platform);
    }

    /**
     * @expectedException \Netframe\Media\Import\UnsupportedUrlException
     */
    public function testImportNotSupported()
    {
        $manager = new MediaManager(
            new ImporterRegistry(),
            $this->getMockBuilder('Netframe\Media\Upload\FileTypeRegistry')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Netframe\Media\Upload\FileSystemRegistry')->disableOriginalConstructor()->getMock(),
            $this->getMock('Netframe\Media\Upload\FileKeyGeneratorInterface')
        );

        $manager->import('foo.bar');
    }

    public function testUpload()
    {
        $fileKeyGenerator = $this->getMock('Netframe\Media\Upload\FileKeyGeneratorInterface');
        $fileKeyGenerator
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('foo'));

        $filesystem = $this->getMockBuilder('Netframe\Media\Upload\FileSystemInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $movedFile = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->getMock();

        $movedFile
            ->expects($this->once())
            ->method('getRealPath')
            ->will($this->returnValue('/path/to/file/foo.jpg'));

        $filesystem
            ->expects($this->once())
            ->method('move')
            ->will($this->returnValue($movedFile));

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $file
            ->expects($this->once())
            ->method('getClientOriginalName')
            ->will($this->returnValue('original-name'));

        $file
            ->expects($this->once())
            ->method('getClientMimeType')
            ->will($this->returnValue('image/jpeg'));

        $file
            ->expects($this->once())
            ->method('getClientOriginalExtension')
            ->will($this->returnValue('jpg'));

        $fileTypeRegistry = new FileTypeRegistry();
        $fileTypeRegistry->addFileType(new FileType(Media::TYPE_IMAGE, 'jpeg', 'image/jpeg'));
        $fileTypeRegistry->addFileType(new FileType(Media::TYPE_IMAGE, 'jpg', 'image/jpeg'));

        $fileSystemRegistry = new FileSystemRegistry();
        $fileSystemRegistry->addFileSystem($filesystem, Media::TYPE_IMAGE);

        $manager = new MediaManager(
            $this->getMockBuilder('Netframe\Media\Import\ImporterRegistry')->disableOriginalConstructor()->getMock(),
            $fileTypeRegistry,
            $fileSystemRegistry,
            $fileKeyGenerator
        );

        $media = $manager->upload($file);

        $this->assertEquals('original-name', $media->name);
        $this->assertEquals(Media::TYPE_IMAGE, $media->type);
        $this->assertEquals('foo.jpg', $media->file_name);
        $this->assertEquals('/path/to/file/foo.jpg', $media->file_path);
        $this->assertInstanceOf('\DateTime', $media->date);
        $this->assertEquals('local', $media->platform);
    }

    /**
     * @expectedException \Netframe\Media\Upload\UnsupportedFileTypeException
     */
    public function testUploadInvalidFileType()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $file
            ->expects($this->once())
            ->method('getClientMimeType')
            ->will($this->returnValue('hackzor/php'));

        $file
            ->expects($this->once())
            ->method('getClientOriginalExtension')
            ->will($this->returnValue('php'));

        $manager = new MediaManager(
            $this->getMockBuilder('Netframe\Media\Import\ImporterRegistry')->disableOriginalConstructor()->getMock(),
            new FileTypeRegistry(),
            new FileSystemRegistry(),
            $this->getMock('Netframe\Media\Upload\FileKeyGeneratorInterface')
        );

        $manager->upload($file);
    }
}
