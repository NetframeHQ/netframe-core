<?php

namespace Netframe\Media\Tests\Import;

use Netframe\Media\Import\YoutubeImporter;

class YoutubeImporterTest extends AbstractImporterTestCase
{
    protected function getImporter()
    {
        return new YoutubeImporter();
    }

    protected function provideSupportedUrls()
    {
        return array(
            array('youtube.com/v/vidid', 'vidid'),
            array('youtube.com/vi/vidid', 'vidid'),
            array('youtube.com/?v=vidid', 'vidid'),
            array('youtube.com/?vi=vidid', 'vidid'),
            array('youtube.com/watch?v=vidid', 'vidid'),
            array('youtube.com/watch?vi=vidid', 'vidid'),
            array('youtu.be/vidid', 'vidid'),
            array('youtube.com/embed/vidid', 'vidid'),
            array('http://youtube.com/v/vidid', 'vidid'),
            array('http://www.youtube.com/v/vidid', 'vidid'),
            array('https://www.youtube.com/v/vidid', 'vidid'),
            array('youtube.com/watch?v=vidid&wtv=wtv', 'vidid'),
        );
    }

    protected function provideUnsupportedUrls()
    {
        return array(
            array('google.fr'),
        );
    }
}
