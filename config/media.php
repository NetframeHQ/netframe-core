<?php

use Netframe\Media\Model\Media;

return [

    /*
     |--------------------------------------------------------------------------
     | Default folders.
     |--------------------------------------------------------------------------
     |
     */
    'default_folders' => [
        0 => [
            'name' => '__profile_medias',
            'rights' => 'rw-rw-r--',
        ],
        1 => [
            'name' => '__posts_medias',
            'rights' => 'rw-rw-r--',
        ],
    ],


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
            ['extension' => 'bmp', 'mime_type' => 'image/bmp'],
            ['extension' => 'bmp', 'mime_type' => 'image/x-ms-bmp'],
            ['extension' => 'jfif', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpe', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpe', 'mime_type' => 'image/pjpeg'],
            ['extension' => 'jpeg', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpeg', 'mime_type' => 'image/pjpeg'],
            ['extension' => 'jpg', 'mime_type' => 'image/jpeg'],
            ['extension' => 'jpg', 'mime_type' => 'image/pjpeg'],
            ['extension' => 'heic', 'mime_type' => 'image/heic'],
            ['extension' => 'png', 'mime_type' => 'image/png'],
            ['extension' => 'gif', 'mime_type' => 'image/gif'],
            ['extension' => 'webp', 'mime_type' => 'image/webp'],
        ],

        Media::TYPE_AUDIO => [
            ['extension' => 'aac', 'mime_type' => 'audio/aac'],
            ['extension' => 'flac', 'mime_type' => 'audio/flac'],
            ['extension' => 'm4a', 'mime_type' => 'audio/mp4'],
            ['extension' => 'm4a', 'mime_type' => 'audio/x-m4a'],
            ['extension' => 'mp2', 'mime_type' => 'audio/mpeg'],
            ['extension' => 'mp3', 'mime_type' => 'audio/mpeg'],
            ['extension' => 'mp3', 'mime_type' => 'audio/x-mpeg'],
            ['extension' => 'mp3', 'mime_type' => 'audio/mpeg3'],
            ['extension' => 'mp3', 'mime_type' => 'audio/mp3'],
            ['extension' => 'mp4', 'mime_type' => 'audio/mp4'],
            ['extension' => 'ogg', 'mime_type' => 'audio/ogg'],
            ['extension' => 'opus', 'mime_type' => 'audio/ogg'],
            ['extension' => 'wav', 'mime_type' => 'audio/wav'],
            ['extension' => 'wav', 'mime_type' => 'audio/x-wav'],
            ['extension' => 'wma', 'mime_type' => 'audio/x-ms-wma'],
        ],

        Media::TYPE_DOCUMENT => [
            ['extension' => 'csv', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'csv', 'mime_type' => 'application/octet-stream'],
            ['extension' => 'doc', 'mime_type' => 'application/msword'],
            [
                'extension' => 'docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            ['extension' => 'dot', 'mime_type' => 'application/msword'],
            ['extension' => 'eml', 'mime_type' => 'message/rfc822'],
            ['extension' => 'key', 'mime_type' => 'application/x-iwork-keynote-sffkey'],
            ['extension' => 'msg', 'mime_type' => 'application/vnd.ms-outlook'],
            ['extension' => 'numbers', 'mime_type' => 'application/x-iwork-keynote-sffnumbers'],
            ['extension' => 'odb', 'mime_type' => 'application/vnd.oasis.opendocument.database'],
            ['extension' => 'odc', 'mime_type' => 'application/vnd.oasis.opendocument.chart'],
            ['extension' => 'odf', 'mime_type' => 'application/vnd.oasis.opendocument.formula'],
            ['extension' => 'odg', 'mime_type' => 'application/vnd.oasis.opendocument.graphics'],
            ['extension' => 'odi', 'mime_type' => 'application/vnd.oasis.opendocument.image'],
            ['extension' => 'odm', 'mime_type' => 'application/vnd.oasis.opendocument.text-master'],
            ['extension' => 'odp', 'mime_type' => 'application/vnd.oasis.opendocument.presentation'],
            ['extension' => 'odt', 'mime_type' => 'application/vnd.oasis.opendocument.text'],
            ['extension' => 'ods', 'mime_type' => 'application/vnd.oasis.opendocument.spreadsheet'],
            ['extension' => 'oft', 'mime_type' => 'application/vnd.ms-outlook'],
            ['extension' => 'otg', 'mime_type' => 'application/vnd.oasis.opendocument.graphics-template'],
            ['extension' => 'otp', 'mime_type' => 'application/vnd.oasis.opendocument.presentation-template'],
            ['extension' => 'ots', 'mime_type' => 'application/vnd.oasis.opendocument.spreadsheet-template'],
            ['extension' => 'ott', 'mime_type' => 'application/vnd.oasis.opendocument.text-template'],
            ['extension' => 'pages', 'mime_type' => 'application/zip'],
            ['extension' => 'pages', 'mime_type' => 'application/x-iwork-pages-sffpages'],
            ['extension' => 'pdf', 'mime_type' => 'application/pdf'],
            ['extension' => 'pot', 'mime_type' => 'application/mspowerpoint'],
            ['extension' => 'pot', 'mime_type' => 'application/vnd.ms-powerpoint'],
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
            ['extension' => 'ppz', 'mime_type' => 'application/mspowerpoint'],
            ['extension' => 'pub', 'mime_type' => 'application/x-mspublisher'],
            ['extension' => 'pub', 'mime_type' => 'application/vnd.ms-publisher'],
            ['extension' => 'pwz', 'mime_type' => 'application/vnd.ms-powerpoint'],
            ['extension' => 'rt', 'mime_type' => 'text/richtext'],
            ['extension' => 'rt', 'mime_type' => 'text/vnd.rn-realtext'],
            ['extension' => 'rtf', 'mime_type' => 'application/msword'],
            ['extension' => 'rtf', 'mime_type' => 'application/rtf'],
            ['extension' => 'rtf', 'mime_type' => 'text/rtf'],
            ['extension' => 'rtf', 'mime_type' => 'application/x-rtf'],
            ['extension' => 'rtf', 'mime_type' => 'text/richtext'],
            ['extension' => 'rtx', 'mime_type' => 'application/rtf'],
            ['extension' => 'rtx', 'mime_type' => 'text/richtext'],
            ['extension' => 'text', 'mime_type' => 'text/plain'],
            ['extension' => 'thmx', 'mime_type' => 'application/vnd.ms-officetheme'],
            ['extension' => 'txt', 'mime_type' => 'text/plain'],
            ['extension' => 'wps', 'mime_type' => 'application/vnd.ms-works'],
            ['extension' => 'xl', 'mime_type' => 'application/excel'],
            ['extension' => 'xla', 'mime_type' => 'application/excel'],
            ['extension' => 'xla', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xla', 'mime_type' => 'application/x-msexcel'],
            ['extension' => 'xlb', 'mime_type' => 'application/excel'],
            ['extension' => 'xlb', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xlb', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xlc', 'mime_type' => 'application/excel'],
            ['extension' => 'xlc', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xlc', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xld', 'mime_type' => 'application/excel'],
            ['extension' => 'xld', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xlk', 'mime_type' => 'application/excel'],
            ['extension' => 'xlk', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xll', 'mime_type' => 'application/excel'],
            ['extension' => 'xll', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xll', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xlm', 'mime_type' => 'application/excel'],
            ['extension' => 'xlm', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xlm', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xls', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xlsm', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['extension' => 'xlsx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['extension' => 'xlt', 'mime_type' => 'application/excel'],
            ['extension' => 'xlt', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xlv', 'mime_type' => 'application/excel'],
            ['extension' => 'xlv', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xlw', 'mime_type' => 'application/excel'],
            ['extension' => 'xlw', 'mime_type' => 'application/vnd.ms-excel'],
            ['extension' => 'xlw', 'mime_type' => 'application/x-excel'],
            ['extension' => 'xlw', 'mime_type' => 'application/x-msexcel'],
        ],

        Media::TYPE_VIDEO => [
            ['extension' => '3gp', 'mime_type' => 'video/3gpp'],
            ['extension' => 'avi', 'mime_type' => 'video/avi'],
            ['extension' => 'avi', 'mime_type' => 'video/msvideo'],
            ['extension' => 'avi', 'mime_type' => 'video/x-msvideo'],
            ['extension' => 'avi', 'mime_type' => 'application/x-troff-msvideo'],
            ['extension' => 'flv', 'mime_type' => 'video/x-flv'],
            ['extension' => 'm4v', 'mime_type' => 'video/mp4'],
            ['extension' => 'mkv', 'mime_type' => 'video/x-matroska'],
            ['extension' => 'mov', 'mime_type' => 'video/quicktime'],
            ['extension' => 'mp2', 'mime_type' => 'video/mpeg'],
            ['extension' => 'mp2', 'mime_type' => 'video/x-mpeg'],
            ['extension' => 'mp2', 'mime_type' => 'video/x-mpeq2a'],
            ['extension' => 'mp4', 'mime_type' => 'video/mp4'],
            ['extension' => 'mpeg', 'mime_type' => 'video/mpeg'],
            ['extension' => 'mpg', 'mime_type' => 'video/mpeg'],
            ['extension' => 'mts', 'mime_type' => 'video/mts'],
            ['extension' => 'mts', 'mime_type' => 'video/avchd-stream'],
            ['extension' => 'mts', 'mime_type' => 'video/vnd.dlna.mpeg-tts'],
            ['extension' => 'wmv', 'mime_type' => 'video/x-ms-wmv'],
        ],

        Media::TYPE_ARCHIVE => [
            ['extension' => 'arj', 'mime_type' => 'application/arj'],
            ['extension' => 'boz', 'mime_type' => 'application/x-bzip2'],
            ['extension' => 'bz', 'mime_type' => 'application/x-bzip'],
            ['extension' => 'bz2', 'mime_type' => 'application/x-bzip2'],
            ['extension' => 'gz', 'mime_type' => 'application/x-compressed'],
            ['extension' => 'gz', 'mime_type' => 'application/x-gzip'],
            ['extension' => 'gzip', 'mime_type' => 'application/x-gzip'],
            ['extension' => 'gzip', 'mime_type' => 'multipart/x-gzip'],
            ['extension' => 'hqx', 'mime_type' => 'application/binhex'],
            ['extension' => 'hqx', 'mime_type' => 'application/binhex4'],
            ['extension' => 'hqx', 'mime_type' => 'application/mac-binhex'],
            ['extension' => 'hqx', 'mime_type' => 'application/mac-binhex40'],
            ['extension' => 'hqx', 'mime_type' => 'application/x-binhex40'],
            ['extension' => 'hqx', 'mime_type' => 'application/x-mac-binhex40'],
            ['extension' => 'rar', 'mime_type' => 'application/x-rar-compressed'],
            ['extension' => 'rar', 'mime_type' => 'application/x-rar'],
            ['extension' => 'shar', 'mime_type' => 'application/x-bsh'],
            ['extension' => 'shar', 'mime_type' => 'application/x-shar'],
            ['extension' => 'tar', 'mime_type' => 'application/x-tar'],
            ['extension' => 'tgz', 'mime_type' => 'application/gnutar'],
            ['extension' => 'tgz', 'mime_type' => 'application/x-compressed'],
            ['extension' => 'z', 'mime_type' => 'application/x-compress'],
            ['extension' => 'z', 'mime_type' => 'application/x-compressed'],
            ['extension' => 'zip', 'mime_type' => 'application/x-compressed'],
            ['extension' => 'zip', 'mime_type' => 'application/x-zip-compressed'],
            ['extension' => 'zip', 'mime_type' => 'application/zip'],
            ['extension' => 'zip', 'mime_type' => 'multipart/x-zip'],
        ],

        Media::TYPE_APPLICATION => [
            ['extension' => 'air', 'mime_type' => 'application/vnd.adobe.air-application-installer-package+zip'],
            ['extension' => 'bin', 'mime_type' => 'application/mac-binary'],
            ['extension' => 'bin', 'mime_type' => 'application/macbinary'],
            ['extension' => 'bin', 'mime_type' => 'application/x-binary'],
            ['extension' => 'bin', 'mime_type' => 'application/x-macbinary'],
            ['extension' => 'com', 'mime_type' => 'application/octet-stream'],
            ['extension' => 'com', 'mime_type' => 'text/plain'],
            ['extension' => 'deb', 'mime_type' => 'application/x-debian-package'],
            ['extension' => 'dmg', 'mime_type' => 'application/x-apple-diskimage'],
            ['extension' => 'exe', 'mime_type' => 'application/x-dosexec'],
            ['extension' => 'mpkg', 'mime_type' => 'application/vnd.apple.installer+xml'],
            ['extension' => 'swf', 'mime_type' => 'application/x-shockwave-flash'],
        ],

        Media::TYPE_SCRIPT => [
            ['extension' => 'asp', 'mime_type' => 'text/asp'],
            ['extension' => 'c', 'mime_type' => 'text/x-c'],
            ['extension' => 'c', 'mime_type' => 'text/plain'],
            ['extension' => 'c++', 'mime_type' => 'text/plain'],
            ['extension' => 'class', 'mime_type' => 'application/java'],
            ['extension' => 'class', 'mime_type' => 'application/java-byte-code'],
            ['extension' => 'class', 'mime_type' => 'application/x-java-class'],
            ['extension' => 'conf', 'mime_type' => 'text/plain'],
            ['extension' => 'cpp', 'mime_type' => 'text/x-c'],
            ['extension' => 'csh', 'mime_type' => 'application/x-csh'],
            ['extension' => 'css', 'mime_type' => 'application/x-pointplus'],
            ['extension' => 'css', 'mime_type' => 'text/css'],
            ['extension' => 'css', 'mime_type' => 'text/plain'],
            ['extension' => 'dump', 'mime_type' => 'application/octet-stream'],
            ['extension' => 'es', 'mime_type' => 'application/ecmascript'],
            ['extension' => 'h', 'mime_type' => 'text/plain'],
            ['extension' => 'h', 'mime_type' => 'text/x-h'],
            ['extension' => 'hh', 'mime_type' => 'text/plain'],
            ['extension' => 'hh', 'mime_type' => 'text/x-h'],
            ['extension' => 'htm', 'mime_type' => 'text/html'],
            ['extension' => 'html', 'mime_type' => 'text/html'],
            ['extension' => 'htmls', 'mime_type' => 'text/html'],
            ['extension' => 'htt', 'mime_type' => 'text/webviewhtml'],
            ['extension' => 'htx', 'mime_type' => 'text/html'],
            ['extension' => 'inf', 'mime_type' => 'application/inf'],
            ['extension' => 'jav', 'mime_type' => 'text/x-java-source'],
            ['extension' => 'java', 'mime_type' => 'text/x-java-source'],
            ['extension' => 'js', 'mime_type' => 'application/x-javascript'],
            ['extension' => 'js', 'mime_type' => 'application/javascript'],
            ['extension' => 'js', 'mime_type' => 'application/ecmascript'],
            ['extension' => 'js', 'mime_type' => 'text/javascript'],
            ['extension' => 'js', 'mime_type' => 'text/ecmascript'],
            ['extension' => 'json', 'mime_type' => 'application/json'],
            ['extension' => 'latex', 'mime_type' => 'application/x-latex'],
            ['extension' => 'less', 'mime_type' => 'text/plain'],
            ['extension' => 'log', 'mime_type' => 'text/plain'],
            ['extension' => 'p', 'mime_type' => 'text/x-pascal'],
            ['extension' => 'pas', 'mime_type' => 'text/pascal'],
            ['extension' => 'php', 'mime_type' => 'text/php'],
            ['extension' => 'php', 'mime_type' => 'text/x-php'],
            ['extension' => 'php', 'mime_type' => 'application/php'],
            ['extension' => 'php', 'mime_type' => 'application/x-php'],
            ['extension' => 'php', 'mime_type' => 'application/x-httpd-php'],
            ['extension' => 'php', 'mime_type' => 'application/x-httpd-php-source'],
            ['extension' => 'pl', 'mime_type' => 'text/plain'],
            ['extension' => 'pl', 'mime_type' => 'text/x-script.perl'],
            ['extension' => 'pm', 'mime_type' => 'text/x-script.perl-module'],
            ['extension' => 'py', 'mime_type' => 'text/x-script.phyton'],
            ['extension' => 'pyc', 'mime_type' => 'application/x-bytecode.python'],
            ['extension' => 'sass', 'mime_type' => 'text/plain'],
            ['extension' => 'sh', 'mime_type' => 'application/x-bsh'],
            ['extension' => 'sh', 'mime_type' => 'application/x-sh'],
            ['extension' => 'sh', 'mime_type' => 'application/x-shar'],
            ['extension' => 'sh', 'mime_type' => 'text/x-script.sh'],
            ['extension' => 'shtml', 'mime_type' => 'text/html'],
            ['extension' => 'shtml', 'mime_type' => 'text/x-server-parsed-html'],
            ['extension' => 'xml', 'mime_type' => 'text/xml'],
        ],

        Media::TYPE_OTHER => [
            ['extension' => 'ai', 'mime_type' => 'application/postscript'],
            ['extension' => 'dwg', 'mime_type' => 'image/vnd.dwg'],
            ['extension' => 'eps', 'mime_type' => 'application/postscript'],
            ['extension' => 'ico', 'mime_type' => 'image/x-icon'],
            ['extension' => 'indd', 'mime_type' => 'application/x-indesign'],
            ['extension' => 'pm4', 'mime_type' => 'application/x-pagemaker'],
            ['extension' => 'pm5', 'mime_type' => 'application/x-pagemaker'],
            ['extension' => 'ps', 'mime_type' => 'application/postscript'],
            ['extension' => 'psd', 'mime_type' => 'image/vnd.adobe.photoshop'],
            ['extension' => 'tif', 'mime_type' => 'image/tiff'],
            ['extension' => 'tif', 'mime_type' => 'image/x-tiff'],
            ['extension' => 'tiff', 'mime_type' => 'image/tiff'],
            ['extension' => 'tiff', 'mime_type' => 'image/x-tiff'],
        ],

        Media::TYPE_FONT => [
            ['extension' => 'eot', 'mime_type' => 'application/vnd.ms-fontobject'],
            ['extension' => 'otf', 'mime_type' => 'application/x-font-opentype'],
            ['extension' => 'svg', 'mime_type' => 'image/svg+xml'],
            ['extension' => 'sfnt', 'mime_type' => 'application/font-sfnt'],
            ['extension' => 'ttf', 'mime_type' => 'application/x-font-ttf'],
            ['extension' => 'ttf', 'mime_type' => 'application/x-font-truetype'],
            ['extension' => 'woff', 'mime_type' => 'application/font-woff'],
            ['extension' => 'woff2', 'mime_type' => 'application/font-woff2'],
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

    'tmp_storage' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/tmp',

    'file_systems' => [

        Media::TYPE_IMAGE => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/images',
        ],

        Media::TYPE_AUDIO => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/tmp',
        ],

        Media::TYPE_DOCUMENT => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/documents',
        ],

        Media::TYPE_VIDEO => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/tmp',
        ],

        Media::TYPE_ARCHIVE => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/archives',
        ],

        Media::TYPE_APPLICATION => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/applications',
        ],

        Media::TYPE_SCRIPT => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/scripts',
        ],

        Media::TYPE_OTHER => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/others',
        ],

        Media::TYPE_FONT => [
            'adapter' => 'local',
            'path' => env('NETFRAME_DATA_PATH', base_path()) . '/storage/uploads/fonts',
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
        'Community' => 'community',
        'House' => 'houses',
        'Project' => 'projects',
        'User' => 'users',
    ],

    /**
     * Proxy (Apache/Nginx) header for file sending
     */
    'proxy_file_sending_header' => env('PROXY_FILE_SENDING_HEADER', 'X-Sendfile')
];
