<?php

return [
    "themes" => [
        "standard" => [
            "name" => "Netframe",
            "slug" => "netframe",
            "type" => "base",
            "path" => "css/style.css",
            "switchable" => true,
            "baseColors" => [
                "light" => [
                    'primaryColor' => '61, 81, 101',
                    'bgColor' => '247, 246, 243',
                    'accentColor' => '255, 77, 77',
                    'baseColor' => '255, 255, 255',
                ],
                "dark" => [
                    'primaryColor' => '179, 182, 191',
                    'bgColor' => '32, 33, 36',
                    'accentColor' => '255, 77, 77',
                    'baseColor' => '42, 42, 46',
                ],
            ],
            "logo" => [
                'light' => '',
                'dark' => '',
            ],
        ],
        "pink" => [
            "name" => "Pink",
            "slug" => "pink",
            "type" => "additionnal",
            "path" => "css/theme/pink/style.css",
            "switchable" => true,
            "baseColors" => [
                "light" => [
                    'primaryColor' => '23, 0, 54',
                    'bgColor' => '255, 255, 255',
                    'accentColor' => '253, 1, 144',
                    'baseColor' => '255, 255, 255',
                ],
                "dark" => [
                    'primaryColor' => '189, 172, 220',
                    'bgColor' => '23, 0, 54',
                    'accentColor' => '253, 1, 144',
                    'baseColor' => '61, 38, 104',
                ],
            ],
            "logo" => [
                'light' => '',
                'dark' => '',
            ],
        ],
        "blue" => [
            "name" => "Blue",
            "slug" => "blue",
            "type" => "additionnal",
            "path" => "css/theme/blue/style.css",
            "switchable" => true,
            "baseColors" => [
                "light" => [
                    'primaryColor' => '59, 80, 99',
                    'bgColor' => '241, 245, 249',
                    'accentColor' => '18, 46, 65',
                    'baseColor' => '255, 255, 255',
                ],
                "dark" => [
                    'primaryColor' => '205, 225, 255',
                    'bgColor' => '18, 46, 65',
                    'accentColor' => '147, 176, 215',
                    'baseColor' => '30, 64, 92',
                ],
            ],
            "logo" => [
                'light' => '',
                'dark' => '',
            ],
        ],
    ],
    "groups" => [
        "standards" => [
            'standard',
            'pink',
            'blue',
        ],
        /*
        "colored" => [
            'pink',
        ],
        */
    ],
];
