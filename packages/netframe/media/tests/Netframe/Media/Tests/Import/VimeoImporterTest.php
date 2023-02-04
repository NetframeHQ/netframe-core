<?php

namespace Netframe\Media\Tests\Import;

use Netframe\Media\Import\VimeoImporter;

class VimeoImporterTest extends AbstractImporterTestCase
{
    protected function getImporter()
    {
        return new VimeoImporter();
    }

    protected function provideSupportedUrls()
    {
        return array(
            array('http://vimeo.com/6701902', '6701902'),
            array('http://vimeo.com/6701902', '6701902'),
            array('http://player.vimeo.com/video/6701902', '6701902'),
            array('http://player.vimeo.com/video/6701902', '6701902'),
            array('http://player.vimeo.com/video/6701902?title=0&amp;byline=0&amp;portrait=0', '6701902'),
            array('http://player.vimeo.com/video/6701902?title=0&amp;byline=0&amp;portrait=0', '6701902'),
            array('http://vimeo.com/channels/vimeogirls/6701902', '6701902'),
            array('http://vimeo.com/channels/vimeogirls/6701902', '6701902'),
            array('http://vimeo.com/channels/staffpicks/6701902', '6701902'),
            array('http://vimeo.com/6701902', '6701902'),
            array('http://vimeo.com/channels/vimeogirls/6701902', '6701902'),
        );
    }

    protected function provideUnsupportedUrls()
    {
        return array(
            array('http://vimeo.com/videoschool'),
            array('http://vimeo.com/videoschool/archive/behind_the_scenes'),
            array('http://vimeo.com/forums/screening_room'),
            array('http://vimeo.com/forums/screening_room/topic:42708'),
        );
    }
}
