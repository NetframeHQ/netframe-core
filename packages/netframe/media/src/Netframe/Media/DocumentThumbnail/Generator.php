<?php

namespace Netframe\Media\DocumentThumbnail;

use Netframe\Media\Model\Media;

interface Generator
{
    public function execute(Media $media): Thumbnail;
}
