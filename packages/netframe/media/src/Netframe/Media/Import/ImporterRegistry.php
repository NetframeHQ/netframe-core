<?php

namespace Netframe\Media\Import;

/**
 * Holds all media importer instances.
 */
class ImporterRegistry
{
    /**
     * @var ImporterInterface[]
     */
    private $importers = array();

    /**
     * Adds an importer to the registry.
     *
     * @param ImporterInterface $mediaImporter
     */
    public function addImporter(ImporterInterface $mediaImporter)
    {
        $this->importers[] = $mediaImporter;
    }

    /**
     * Gets the importers.
     *
     * @return ImporterInterface[]
     */
    public function getImporters()
    {
        return $this->importers;
    }
}
