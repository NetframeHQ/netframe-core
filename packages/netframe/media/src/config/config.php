<?php

use Netframe\Media\Model\Media;

return [

    /*
    |--------------------------------------------------------------------------
    | Media importers.
    |--------------------------------------------------------------------------
    |
    */
    'importers' => [
        'Netframe\Media\Import\DailymotionImporter',
        'Netframe\Media\Import\VimeoImporter',
        'Netframe\Media\Import\YoutubeImporter',
        'Netframe\Media\Import\SoundcloudImporter',
    ],

    /*
    |--------------------------------------------------------------------------
    | File key generator.
    |--------------------------------------------------------------------------
    |
    */
    'file_key_generator' => 'timed_sha1',

    /*
    |--------------------------------------------------------------------------
    | File types.
    |--------------------------------------------------------------------------
    |
    | Maps an internal "media type" to a mime type and an extension used to
    | verify uploaded files and to reject them for security purposes.
    |
    | http://www.sitepoint.com/web-foundations/mime-types-complete-list/
    |
    */
    'file_types' => [

        Media::TYPE_IMAGE => [
            ['extension' => 'jpe', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpe', 'mime_type' => 'image/pjpeg'],
            ['extension' => 'jpeg', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpeg', 'mime_type' => 'image/pjpeg'],
            ['extension' => 'jpg', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpg', 'mime_type' => 'image/pjpeg'],
            ['extension' => 'png', 'mime_type' => 'image/png'],
            ['extension' => 'gif', 'mime_type' => 'image/gif'],
        ],

        Media::TYPE_AUDIO => [
            ['extension' => 'mp3', 'mime_type' => 'audio/mpeg'],
            ['extension' => 'mp3', 'mime_type' => 'audio/mp3'],
            ['extension' => 'wav', 'mime_type' => 'audio/wav'],
            ['extension' => 'ogg', 'mime_type' => 'audio/ogg'],
            ['extension' => 'flac', 'mime_type' => 'audio/flac'],
            ['extension' => 'wma', 'mime_type' => 'audio/x-ms-wma'],
            ['extension' => 'm4a', 'mime_type' => 'audio/mp4'],
            ['extension' => 'mp4', 'mime_type' => 'audio/mp4'],
        ],

        Media::TYPE_DOCUMENT => [
            ['extension' => 'pdf', 'mime_type' => 'application/pdf'],
            ['extension' => 'doc', 'mime_type' => 'application/msword'],
            [
                'extension' => 'docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            ['extension' => 'xls', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xlsx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['extension' => 'pps', 'mime_type' => 'application/vnd.ms-powerpoint'],
            [
                'extension' => 'ppsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow'
            ],
            ['extension' => 'ppt', 'mime_type' => 'application/vnd.ms-powerpoint'],
            [
                'extension' => 'pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ],
            ['extension' => 'odt', 'mime_type' => 'application/vnd.oasis.opendocument.text'],
            ['extension' => 'odt', 'mime_type' => 'application/vnd.oasis.opendocument.text'],
        ],

        Media::TYPE_VIDEO => [
            ['extension' => 'mp4', 'mime_type' => 'video/mp4'],
            ['extension' => 'avi', 'mime_type' => 'video/avi'],
            ['extension' => 'avi', 'mime_type' => 'video/msvideo'],
            ['extension' => 'mov', 'mime_type' => 'video/quicktime'],
            ['extension' => '3gp', 'mime_type' => 'video/3gpp'],
            ['extension' => 'mpeg', 'mime_type' => 'video/mpeg'],
            ['extension' => 'mpg', 'mime_type' => 'video/mpeg'],
            ['extension' => 'wmv', 'mime_type' => 'video/x-ms-wmv'],
            ['extension' => 'mts', 'mime_type' => 'video/mts'],
            ['extension' => 'flv', 'mime_type' => 'video/x-flv'],
            ['extension' => 'm4v', 'mime_type' => 'video/mp4'],
        ],

        Media::TYPE_ARCHIVE => [
            ['extension' => 'zip', 'mime_type' => 'application/zip'],
            ['extension' => 'rar', 'mime_type' => 'application/x-rar-compressed'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File systems.
    |--------------------------------------------------------------------------
    |
    | Configuration for the filesystems used to managed each media type.
    | The available adapters are: 'local'.
    |
    */

    'tmp_storage' => app_path() . '../storage/uploads/tmp',

    'file_systems' => [

        Media::TYPE_IMAGE => [
            'adapter' => 'local',
            'path' => app_path() . '../storage/uploads/images',
        ],

        Media::TYPE_AUDIO => [
            'adapter' => 'local',
            'path' => app_path() . '../storage/uploads/tmp',
        ],

        Media::TYPE_DOCUMENT => [
            'adapter' => 'local',
            'path' => app_path() . '../storage/uploads/documents',
        ],

        Media::TYPE_VIDEO => [
            'adapter' => 'local',
            'path' => app_path() . '../storage/uploads/tmp',
        ],

        Media::TYPE_ARCHIVE => [
            'adapter' => 'local',
            'path' => app_path() . '../storage/uploads/archives',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Edit media page.
    |--------------------------------------------------------------------------
    |
    */
    'edit_media_page' => [

        'video_player_width' => '100%',
        'video_player_height' => '460px',
    ],

    /*
     |--------------------------------------------------------------------------
     | Model owner relation.
     |--------------------------------------------------------------------------
     |
     */
    'owner_relation' => [
        'Angel' => 'angels',
        'Community' => 'community',
        'House' => 'houses',
        'Project' => 'projects',
        'Talent' => 'talents',
        'User' => 'users',
    ]
];
