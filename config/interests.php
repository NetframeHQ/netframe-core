<?php

return [
    "equivalence" => [
        "TEvent" => "post",
        "Offer" => "post",
        "News" => "post",
        "Project" => "profile",
        "House" => "profile",
        "Community" => "profile",
        "Media" => "media",
        "UsersReference" => "reference",
    ],

    "profile" => [
        "create" => [
            "new" => 1.8,
            "exists" => 1.08,
        ],
        "participate" => [
            "new" => 1.7,
            "exists" => 1.07,
        ],
        "share" => [
            "new" => 1.6,
            "exists" => 1.06,
        ],
        "comment" => [
            "new" => 1.5,
            "exists" => 1.05,
        ],
        "like" => [
            "new" => 1.4,
            "exists" => 1.04,
        ],
    ],
    "post" => [
        "create" => [
            "new" => 1.7,
            "exists" => 1.07,
        ],
        "participate" => [
            "new" => 1.6,
            "exists" => 1.06,
        ],
        "share" => [
            "new" => 1.5,
            "exists" => 1.05,
        ],
        "comment" => [
            "new" => 1.4,
            "exists" => 1.04,
        ],
        "like" => [
            "new" => 1.3,
            "exists" => 1.03,
        ],
    ],
    "reference" => [
        "create" => [
            "new" => 1.6,
            "exists" => 1.06,
        ],
        "participate" => [
            "new" => 1.5,
            "exists" => 1.05,
        ],
        "share" => [
            "new" => 1.4,
            "exists" => 1.04,
        ],
        "comment" => [
            "new" => 1.3,
            "exists" => 1.03,
        ],
        "like" => [
            "new" => 1.2,
            "exists" => 1.02,
        ],
    ],
    "media" => [
        "create" => [
            "new" => 1.5,
            "exists" => 1.05,
        ],
        "participate" => [
            "new" => 1.4,
            "exists" => 1.04,
        ],
        "share" => [
            "new" => 1.3,
            "exists" => 1.03,
        ],
        "comment" => [
            "new" => 1.2,
            "exists" => 1.02,
        ],
        "like" => [
            "new" => 1.1,
            "exists" => 1.01,
        ],
    ],
];
