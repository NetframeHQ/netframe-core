<?php

return [
    'fmc_key' => env('FIREBASE_KEY'),

    'googleApi' => [
        'key' => env('GOOGLE_API_KEY'),
        'server-key' => env('GOOGLE_API_SERVERKEY'),
    ],

    'youtubeApi' => [
        'key' => env('YOUTUBE_API_KEY'),
        'server-key' => env('YOUTUBE_API_SERVERKEY'),
    ],

    'soundCloud' => [
        'key' => env('SOUNDCLOUD_API'),
    ],

    'vimeoApi' => [
        'clientId' => env('VIMEO_CLIENTID'),
        'clientSecret' => env('VIMEO_CLIENTSECRET'),
    ],

    'dailyMotionApi' => [
        'clientId' => env('DAILYMOTION_CLIENTID'),
        'clientSecret' => env('DAILYMOTION_CLIENTSECRET'),
    ],

    'mailJet' => [
        'publicKey' => env('MAILJET_PUBLICKEY'),
        'secretKey' => env('MAILJET_SECRETKEY'),
    ],

    'jitsi' => [
        'url' => env('JITSI_URL'),
        'iss' => env('JITSI_ISS'),
        'key' => env('JITSI_KEY'),
        'keyMeet' => env('JITSI_KEYMEET'),
    ],
];
