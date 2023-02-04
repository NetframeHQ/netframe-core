<?php

namespace Netframe\Media\Tests\Import;

use Netframe\Media\Tests\TestCase;

abstract class AbstractImporterTestCase extends TestCase
{
    public function testParseIdSupported()
    {
        $importer = $this->getImporter();

        foreach ($this->provideSupportedUrls() as $dataSet) {
            $this->assertEquals($dataSet[1], $importer->parseId($dataSet[0]));
        }
    }

    public function testParseIdUnsupported()
    {
        $importer = $this->getImporter();

        foreach ($this->provideUnsupportedUrls() as $dataSet) {
            $this->assertNull($importer->parseId($dataSet[0]));
        }
    }

    abstract protected function getImporter();
    abstract protected function provideSupportedUrls();
    abstract protected function provideUnsupportedUrls();
}
