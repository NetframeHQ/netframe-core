<?php

Event::listen('media.upload', 'Netframe\Media\Event\CropHandler');
Event::listen('media.import', 'Netframe\Media\Event\CropHandler');
