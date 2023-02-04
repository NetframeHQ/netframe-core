<?php

namespace Netframe\Media\Tests\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Netframe\Media\Upload\TimedSha1FileKeyGenerator;

class TimedSha1FileKeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateUnique()
    {
        $generator = new TimedSha1FileKeyGenerator();

        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $file
            ->expects($this->any())
            ->method('getClientOriginalName')
            ->will($this->returnValue('foo.txt'));

        $this->assertNotEquals($generator->generate($file), $generator->generate($file));
    }
}
