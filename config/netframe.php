<?php
/**
 *
 * ET config files
 *
 *
 */
return array(

    'baseLoginUrl' => env('APP_BASE_PROTOCOL').'://work.'.env('APP_BASE_DOMAIN').'/login',

    'allowedIps' => [
        '178.33.234.43', //encoder
        '192.168.0.51', // encoder vrack ip
    ],

    // type_foreign or list profile available
    "list_profile" => [
        "house",
        "project",
        "community",
    ],

    "list_profile_mono" => [
        "community",
    ],

    "join_relations" => [
        "project",
        "house",
        "community",
        "channel",
    ],

    "members_status" => [
        '0' => 'waiting',
        '1' => 'member',
        '2' => 'guest',
        '3' => 'blacklist',
    ],

    //type_foreign or list profile available for subscribe or like
    "list_profile_follow" => array(
        "project",
        "house",
        "community",
        "user",
        "Project",
        "House",
        "Community",
        "User"
    ),

    // type_post available
    "type_post" => array(
        "news",
        "event",
        "playlist",
    ),

    // List type of like exist
    "type_like" => array(
        "comment",
        "house",
        "project",
        "community",
        "media",
        "news",
        "event",
    ),

    //list model likables without newsfeed update
    "model_likables" => array(
        "App\\Comment",
        "App\\UsersReference",
        "House",
        "Community",
        "Project",
        "User",
        "App\\Media",
        "Netframe\Media\Model\Media",
    ),


    //list of models taggables
    "model_taggables" => [
        "House",
        "Community",
        "Project",
        "TEvent",
        "News",
        "Offer",
        "Media",
        "UsersReference",
        "Channel",
    ],

    //list model for comment profiles and medias NOT for posts
    "model_commentable" => array(
        "Comment",
        "House",
        "Community",
        "Project",
        "User",
        "Netframe\Media\Model\Media",
        "Media",
    ),

    // list profile in Netframe model for get list model geoip location
    "geoip_profiles" => array(
        'users' => [1, 'user', 'User'],
        'projects' => [1, 'project', 'Project'],
        'houses' => [1, 'house', 'House'],
        'community' => [1, 'community', 'Community'],
        'events' => [1, 'event', 'TEvent']
    ),

    "geoip_filters" => array(
        'users',
        'projects',
        'houses',
        'community',
        'events'
    ),

    "buzz_columns" => array(
        "yearly" => "year_score",
        "monthly" => "month_score",
        "weekly" => "week_score",
        "daily" => "day_score",
    ),

    //posts types autorized for share
    "sharePostsTypes" => array(
        'News',
        'TEvent',
        'Playlist',
        'Offer',
        'NetframeAction',
        'TaskTable',
    ),

    //profile types autorized for share
    "shareProfilesTypes" => array(
        'Community',
        'House',
        'Project',
        'TaskTable',
    ),

    //search limits
    "search_limit" => 20,
    // Limited number Post to display on Page News Feeds
    "number_post" => 10,
    "number_comment" => 2,
    //Enable to use elasticsearch for search.
    "enabled_elasticsearch" => true,

    // Abuse reporting list
    "typeAbuse" => array(
        "spam",
        "nudity",
        "speech",
        "copyright",
        "impersonation",
        "fake_profile",
    ),

    "abuseMarker" => array(
        "spam" => "warning",
        "nudity" => "danger",
        "speech" => "warning",
        "copyright" => "info",
        "impersonation" => "primary",
        "fake_profile" => "danger"
    ),

    // Abuse list supported
    "listItemTypeAbuse" => array(
        "Media",
        "News",
        "TEvent",
        "Offer"
    ),

    //for offers
    "offersType" => array(
        "commercial_offer",
        "non_commercial_offer",
        "non_free_demand",
        "free_demand",
    ),

    "offersTypeChoice" => array(
        "offer" => array(
            "commercial_offer",
            "non_commercial_offer",
        ),
        "demand" => array(
            "non_free_demand",
            "free_demand",
            ),
    ),

    "posting" => [
        'News' => 'publish',
        'TEvent' => 'event',
        'Offer' => 'marketplace',
    ],

    'log_mails_data' => env('LOG_MAILS_DATA', false),
);
