<?php


return array(

    "type" => array(

        "friends"           => array(
            "send"              => "askFriends",
            "accepted"          => "friendsOk",
        ),

        "comment"           => array(
            "send"              => "asComment",
        ),

        "memberUpdate" => "memberUpdate",
        "inviteOn" => "inviteOn",
        "acceptInvite" => "acceptInvite",

        "project"           => array(
            "send"              => "joinProject",
            "accepted"          => "joinProjectOk",
            "profile_id"        => "project_id",
            "participant"       => 4,
            "contributor"       => 3,
            "the_Profile"       => 'notifications.the_project',
        ),

        "house"             => array(
            "send"              => "joinHouse",
            "accepted"          => "joinHouseOk",
            "profile_id"        => "houses_id",
            "participant"       => 4,
            "contributor"       => 3,
            "the_Profile"       => 'notifications.the_house',
        ),

        "community"         => array(
            "send"              => "joinCommunity",
            "accepted"          => "joinCommunityOk",
            "profile_id"        => "community_id",
            "participant"       => 4,
            "contributor"       => 3,
            "the_Profile"       => 'notifications.the_community',
        ),

        "channel"             => array(
            "send"              => "joinChannel",
            "accepted"          => "joinChannelOk",
            "profile_id"        => "channels_id",
            "participant"       => 4,
            "contributor"       => 3,
            "the_Profile"       => 'notifications.the_channel',
        ),

        "post"  =>  array(
            "share" => "share",
            "shareProfile" => "shareProfile",
            "shareMedia" => "shareMedia",
        ),

        "user" => array(
            "newReferenceByUser" => 'userNewReferenceByUser',
            "taggued" => "userTaggued',"
        ),

        "actions" => array(
            "likeProfile" => 'likeProfile',
            "likeContent" => 'likeContent',
            "followProfile" => 'followProfile',
            "clipMedia" => 'clipMedia',
            "clipProfile" => 'clipProfile',
        ),

        "event" => array(
            "participateEvent" => "participateEvent",
        ),

        "join" => [
            'project' => "has_join_project",
            'community' => "has_join_community",
            'house' => "has_join_house",
        ],

    ),


);
