<?php

return array(
    //routes not affected in log tracking
    "exclusion-routes-tracking" => [
        "admin*",
        "chat*",
        "media*",
        "posting*",
        "netframe/set-gmt",
        "emoji*",
        "netfame/svg-icon",
    ],

    //subdomains exclusions
    "exclusion-subdomain-tracking" => [
        "broadcast",
    ],
);
