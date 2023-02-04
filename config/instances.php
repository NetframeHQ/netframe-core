<?php

return [
    'default_config' => [
        'boarding_domain' => null,      // email domain provide boarding
        'boarding_invite_key' => null,  // key to provide boarding
        'boarding_on_key_disable' => 0,  // ability to create user account with public key
        'boarding_validate' => null,    // user boarded need to be validate
        'custom_logo_2018' => null,
        'active_bg_login_2018' => 1,
        'active_bg_screen_2018' => 1,
        'billing_offer' => 'free',
        'profile_profile' => '{"user":{"house":1,"community":1,"project":1,"channel":1},'
            . '"manager":{"house":1,"community":1,"project":1,"channel":1},'
            . '"administrator":{"house":1,"community":1,"project":1,"channel":1},'
            . '"house":{"house":1,"community":1,"project":1,"channel":1},'
            . '"community":{"house":1,"community":1,"project":1,"channel":1},'
            . '"project":{"house":1,"community":1,"project":1,"channel":1}}',
        'like_buttons' => '[414,380,381,3,59,31]',
        'monoprofile' => 1,
    ],

    'defaultEmojis' => [
        414,380,381,4,49,57
    ],

    'searchProfiles' => [
        'users' => 'user',
        'projects' => 'project',
        'communities' => 'community',
        'houses' => 'house',
    ],

    'rightsProfiles' => [
        'users' => 'user',
        'managers' => 'manager',
        'administrators' => 'administrator',
        'houses' => 'house',
        'communities' => 'community',
        'projects' => 'project',
    ],

    'rightsMonoProfiles' => [
        'users' => 'user',
        'managers' => 'manager',
        'administrators' => 'administrator',
        'communities' => 'community',
    ],

    'defaultCss' => [
        'backgroundMenu' => '#fff',
        'menuIcon' => '#00a19a',
        'backgroundLogo' => '#fff',
        'principalColor' => '#4b4b4b',
        'principalTextColor' => '#fff',
        'secondaryColor' => '#00a19a',
        //'colorLinks' => '#000000',
        'screenBackground' => '../img/page-background.gif',
        'loginBackground' => '../img/login-background.jpg',
        'loginBgColor' => '#f5f5f5',
        'screenBgColor' => '#f5f5f5',
    ],

    'defaultCss2018' => [
        'backgroundMenu' => '#fff',
        'menuIcon' => '#333',
        'principalColor' => '#333',
        'loginBackground' => '../img/login-background-2018.jpg',
        'loginBgColor' => '#f5f5f5',
    ],

    "medias" => [
        "mediaTypes" => [
            "instance-main-logo" => [
                "parameterName" => "main_logo_2018",
                "storageDir" => "logos",
                "maxSize" => 350,
            ],
            "instance-menu-logo" => [
                "parameterName" => "menu_logo_2018",
                "storageDir" => "logos",
                "maxSize" => 250,
            ],
            "instance-main-logo-dark" => [
                "parameterName" => "main_logo_2018_dark",
                "storageDir" => "logos",
                "maxSize" => 350,
            ],
            "instance-menu-logo-dark" => [
                "parameterName" => "menu_logo_2018_dark",
                "storageDir" => "logos",
                "maxSize" => 250,
            ],
            "instance_background" => [
                "parameterName" => "instance_background",
                "storageDir" => "backgrounds",
                "maxSize" => 2000,
            ],
            "background_login_2018" => [
                "parameterName" => "background_login_2018",
                "storageDir" => "backgrounds",
                "maxSize" => 2000,
            ],
            "cover_image" => [
                "parameterName" => "cover_image",
                "storageDir" => "backgrounds",
                "maxSize" => 2000,
            ],
        ],

        "supportedMimes" => [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
        ],

        "allowedDownload" => [
            "menu_logo_2018",
            "main_logo_2018",
            "menu_logo_2018_dark",
            "main_logo_2018_dark",
            "instance_background",
            "background_login_2018",
            "cover_image",
        ],
    ],
];
