<?php

return array(
    "levelReport" => 2,
    "reportsDirectory" => env('REPORTS_DIRECTORY'),
    "emailAdmin" => [
        'email' => env('ADMIN_EMAIL', 'tech@netframe.fr'),
        'name' => env('ADMIN_NAME', 'Netframe'),
    ],
    "freeAccess" => env('AUTO_ADMIN_CONNECT', false),

);
