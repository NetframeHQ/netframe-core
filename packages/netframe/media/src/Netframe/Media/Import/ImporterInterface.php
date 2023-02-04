<?php

namespace Netframe\Media\Import;

/**
 * Interface for media importers.
 */
interface ImporterInterface
{
    /**
     * Parses the media id from the url.
     *
     * @param string $url
     *
     * @return string|null The id or NULL if cannot be parsed
     */
    public function parseId($url);

    /**
     * Gets the platform name.
     *
     * @return integer
     */
    public function getPlatform();

    /**
     * Gets the description shown to the application users.
     *
     * 'icon' => 'socicon socicon-twitter'
     * 'name' => 'Twitter'
     *
     * @return array
     */
    public function getDescription();
}
