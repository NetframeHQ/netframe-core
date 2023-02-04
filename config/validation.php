<?php

return array(

    "boarding" => [
        "email" => "required|email|unique:boarding",
    ],

    "boarding/code" => [
        "n1" => "required|numeric|between:0,9",
        "n2" => "required|numeric|between:0,9",
        "n3" => "required|numeric|between:0,9",
        "n4" => "required|numeric|between:0,9",
        "n5" => "required|numeric|between:0,9",
        "n6" => "required|numeric|between:0,9",
    ],

    "boarding/instance" => [
        "instance_name" => "required|min:2|max:50",
    ],

    "boarding/invite" => [
        "email1" => 'nullable|email',
        "email2" => 'nullable|email',
        "email3" => 'nullable|email',
        "email4" => 'nullable|email',
        "email5" => 'nullable|email',
    ],

    "admin/admin/add" => [
        "username" => "required",
        "email" => "required|email|unique:admins",
        "password" => "required|min:5",
    ],

    "admin/admin/update" => [
        "username" => "required",
    ],

    "admin/admin/update/email" => [
        "email" => "required|email|unique:admins",
    ],

    "admin/admin/update/password" => [
        "password" => "required|min:5",
    ],

    "auth/register" => array(
        "firstname" => "required|min:2|max:45",
        "name" => "required|min:2|max:45",
        "email" => "required|email|unique:users",
        //"email_confirm" => "required|same:email",
        "password" => "confirmed|required|min:6",
        //"date_birth" => "required|date|date_format:Y-m-d",
        //"gender" => "required|in:man,woman",
        "cgu" => "required|in:1"
    ),

    "auth/login" => array(
        "email" => "required|email",
        "password" => "required"
    ),

    "visitor/register" => array(
        "email" => "required|email"
    ),

    "virtualuser/edit" => array(
        "firstname" => "required|min:2|max:45",
        "lastname" => "required|min:2|max:45",
        "password" => "confirmed|nullable|min:6",
    ),

    "virtualuser/create" => array(
        "password" => "confirmed|required|min:6",
    ),

    "virtualuser/email" => array(
        "email" => "required|email|unique:users|unique:virtual_users",
    ),

    "house/edit" => array(
        "name" => "required",
        //"description" => "required",
        "placeSearch" => "sometimes",
        //"confidentiality" => "required",
        //'free_join' => 'required',
    ),

    "community/edit" => array(
        "name" => "required",
        //"description" => "required",
        "placeSearch" => "sometimes",
        //"confidentiality" => "required",
        //'free_join' => 'required',
    ),

    "channel/newsPost" => array(
        "content" => "required|min:1",
    ),

    "channel/newsPostWithMedias" => array(
        "content" => "nullable",
    ),

    "page/newsPost" => array(
        "content" => "required|min:1",
        "id_foreign" => "required|numeric",
        "type_foreign" => "required|in:house,project,community,user",
        "confidentiality" => "nullable|in:0,1"
    ),

    "page/newsPostWithMedias" => array(
        "content" => "nullable",
        "id_foreign" => "required|numeric",
        "type_foreign" => "required|in:house,project,community,user",
        "confidentiality" => "nullable|in:0,1"
    ),

    "page/newsEdit" => array(
        "content" => "required|min:1",
        "confidentiality" => "nullable|in:0,1"
    ),

    "page/commentPost" => array(
        "content" => "required",
        "author_id" => "required|integer",
        "author_type" => "required|in:house,project,community,user",
        "post_id" => "required|integer",
        "post_type" => "required|in:" . implode(',', [
            'App\NetframeAction',
            'App\Offer',
            'App\News',
            'App\Share',
            'App\TEvent',
            'App\House',
            'App\Project',
            'App\Community',
            'App\User',
            'Netframe\Media\Model\Media',
            'App\Media'
        ]),

        // comment_id rules if field is present
        "comment_id" => "nullable|integer"
    ),

    "page/commentProfile" => array(
        "content" => "required",
        "author_id" => "required|integer",
        "author_type" => "required|in:house,project,community,user",
        "profile_id" => "required|integer",
        "profile_type" => "required|in:house,project,community",

        // comment_id rules if field is present
        "comment_id" => "nullable|required|integer"
    ),

    "page/joinPost" => array(
        /*
        "content" => "required",
        "guest_id" => "required|numeric",
        "guest_type" => "required|in:house,project,community,user",
        */
        "users_id" => "required|numeric",
        "profile_id" => "required|numeric",
        "profile_type" => "required|alpha",
    ),

    "netframe/like" => array(
        "id_like_foreign" => "required|integer",
        "type_like_foreign" => "required|alpha",
        "id_foreign" => "required|integer",
        "type_foreign" => "required|alpha"
    ),

    "user/setting" => array(
        "firstname" => "required|alpha_dash|min:2|max:45",
        "name" => "required|alpha_dash|min:2|max:45",
        //"date_birth" => "required|date|date_format:Y-m-d",
        //"gender" => "required|in:man,woman",
        "lang" => "exists:ref_langs,iso_639_1",
        "pays" => "exists:ref_countries,iso",
    ),

    "profile/favorite" => array(
        "description" => "required|min:2",
        "date" => "required|date|date_format:Y-m-d",
        "id_media" => "required|numeric",
        "id_foreign" => "required|numeric",
        "type_foreign" => "required|alpha"
    ),

    "message/newsMessage" => array(
        "content" => "required|min:1",
        "receiver_id" => "required|integer",
        "receiver_type" => "required|in:house,project,community,user",
    ),

    "interests/add" => array(
    ),

    "user/description" => array(
        "description" => "nullable",
    ),

    "user/training" => array(
        "training" => "nullable",
    ),

    "user/isearch" => array(
        "isearch" => "nullable",
    ),

    "user/ihave" => array(
        "ihave" => "nullable",
    ),

    "share" => array(
        "content" => "nullable",
        "id_newsfeed" => "required|integer",
    ),

    "shareProfile" => array(
        "content" => "nullable",
        "profileId" => "required|integer",
        "profileType" => "required|in:App\Community,App\House,App\Project",
    ),

    "shareMedia" => array(
        // "content" => "required",
        "mediaId" => "required|integer",
    ),

    "playlist/publish" => array(
        "content" => "required",
        "id_playlist" => "required|numeric",
    ),

    "abuse" => array(
        "users_id_property" => "required|integer",
        "post_id" => "required|integer",
        "post_type" => "required|alpha",
        //"type_abuse" => "required|in:".implode(',', \Config::get('netframe.typeAbuse'))
    ),

    "project/bookmark" => array(
        "name" => "required|min:2",
        "url" => "required|url",
        "description" => "required|min:2",
    ),

    "offer" => array(
        "name" => "required",
        "offer_type" => "required|in:offer,demand",
        "content" => "required",
        "start_at" => "date|date_format:Y-m-d",
        "stop_at" => "nullable|date|date_format:Y-m-d",
        "placeSearch" => "sometimes",
        "latitude" => "nullable",
        "longitude" => "nullable",
    ),

    "user/nationality" => array(
        "pays" => "exists:ref_countries,iso",
        "nationality" => "exists:ref_countries,iso",
    ),

    "members/invite" => [
        'users' => "required|array",
        'role' => "required|in:2,3,4,5",
    ],

    "channel/edit" => [
        "name" => "required|max:50",
        "description" => "nullable",
        "id_foreign" => "required|numeric",
        "type_foreign" => "required|in:house,project,community,user",
    ],

    "xplorer" => [
        "addFolder" => [
            "name" => "required",
        ],
    ],

    "instance/groups" => [
        'name' => 'required',
    ],
);
