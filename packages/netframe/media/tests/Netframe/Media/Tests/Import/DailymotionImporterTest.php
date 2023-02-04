<?php

namespace Netframe\Media\Tests\Import;

use Netframe\Media\Import\DailymotionImporter;

class DailymotionImporterTest extends AbstractImporterTestCase
{
    protected function getImporter()
    {
        return new DailymotionImporter();
    }

    protected function provideSupportedUrls()
    {
        return array(
            array('http://www.dailymotion.com/embed/video/vidid', 'vidid'),
            array('http://www.dailymotion.com/video/vidid', 'vidid'),
            array('http://www.dailymotion.com/swf/video/vidid', 'vidid'),
        );
    }

    protected function provideUnsupportedUrls()
    {
        return array(
            array('google.fr'),
        );
    }
}
