<?php

namespace Netframe\Media\Import;

/**
 * Thrown when trying to import from an unsupported url.
 */
class UnsupportedUrlException extends \InvalidArgumentException
{
    public function __construct($url)
    {
        parent::__construct(sprintf('The url "%s" is not supported for import', $url));
    }
}
