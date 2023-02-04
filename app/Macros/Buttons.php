<?php

/**
 *  Button Like Post
 */
HTML::macro('likeBtn', function ($data, $likeThis = false, $nbLike = 0, $class = null, $removeTxt = 0) {
    $tpl = [];
    $tpl['class'] = $class;
    $tpl['removeTxt'] = $removeTxt;
    $tpl['nbLike'] = $nbLike;
    //$tpl['likeThis'] = (isset($data['likeThis'])) ? $data['likeThis'] : null;
    $tpl['likeThis'] = $likeThis;
    $tpl['dataJsonEncoded'] = $data;//json_encode($data);
    $tpl['liked_id'] = $data['liked_id'];
    $tpl['liked_type'] = str_replace('App\\', '', $data['liked_type']);
    if ($likeThis) {
        $like = \App\Like::where('liked_id', $tpl['liked_id'])
            ->where('liked_type', '=', $data['liked_type'])
            ->where('liker_id', $data['liker_id'])
            ->where('liker_type', 'App\\'.ucfirst($data['liker_type']))->first();
        $tpl['like'] = $like;
        if ($like) {
            $emoji = $like->emoji;
            if ($emoji) {
                $tpl['emoji'] = $emoji->value;
            }
        }
    }
    $ids = json_decode(\App\Instance::find(session('instanceId'))->getParameter('like_buttons'), true);
    if ($ids==null) {
        $ids = config('instances.defaultEmojis');//\App\Emoji::limit(5)->get();
    }

    //$emojis = $customLikesEmojis;
    $likes = \App\Emoji::join('likes', function ($joinS) {
                $joinS->on('likes.emojis_id', '=', 'emojis.id');
    })
            ->where('liked_id', $tpl['liked_id'])
            ->where('liked_type', '=', $data['liked_type'])
            ->groupBy('emojis_id')
            ->orderBy(\DB::raw('total', 'DESC'))
            ->orderBy('created_at', 'ASC')
            ->select('emojis.*', \DB::raw('count(*) as total'))
            ->findMany($ids);
    $tpl['likes'] = $likes;
    //$tpl['emojis'] = $emojis;

    return view('macros.button-like', $tpl)->render();
});

/**
 *  Button Like comments
 */
HTML::macro('likeBtnComment', function ($data, $likeThis, $nbLike, $class = null) {
    $tpl = [];
    $tpl['class'] = $class;
    $tpl['nbLike'] = $nbLike;
    //$tpl['likeThis'] = (isset($data['likeThis'])) ? $data['likeThis'] : null;
    $tpl['likeThis'] = $likeThis;
    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['liked_id'] = $data['liked_id'];
    $tpl['liked_type'] = str_replace('App\\', '', $data['liked_type']);

    return view('macros.button-like-comment', $tpl)->render();
});

/**
 * Button like on profile card
 */
HTML::macro('likeBtnProfile', function ($profile, $liked = false, $nbLike = 0, $hideLike = false) {
    $tpl = [];

    $data = [
        'profile_id' => $profile->id,
        'profile_type' => class_basename($profile)
    ];

    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['profile'] = $profile;
    $tpl['liked'] = $liked;
    $tpl['nbLike'] = $nbLike;
    $tpl['hideLike'] = $hideLike;
    $tpl['liked_id'] = $profile->id;
    $tpl['liked_type'] = class_basename($profile);

    return view('macros.button-like-profile', $tpl)->render();
});

/**
 * Button like on profile card
 */
HTML::macro('subscribeBtnProfile', function ($profile, $followed = false, $nbFollowers = 0) {
    $tpl = [];

    $data = [
        'profile_id' => $profile->id,
        'profile_type' => class_basename($profile)
    ];

    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['profile'] = $profile;
    $tpl['followed'] = $followed;
    $tpl['nbFollowers'] = $nbFollowers;

    return view('macros.button-subscribe-profile', $tpl)->render();
});

/**
 * Button add friend
 */
HTML::macro('addFriendBtn', function ($data, $friends = null) {
    $tpl = [];

    $tpl['friends'] = $friends;
    $tpl['dataJsonEncoded'] = json_encode($data);

    return view('macros.join.button-add-friend', $tpl)->render();
});


/**
 * Button answer friend
 */
HTML::macro('askedAnswerBtn', function ($data, $btnSize = '') {
    $tpl = [];

    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['btnSize'] = $btnSize;

    return view('macros.join.button-asked-answer', $tpl)->render();
});

/**
 * Button delete friend
 */
HTML::macro('deleteFriendBtn', function ($data) {
    $tpl = [];

    $tpl['dataJsonEncoded'] = json_encode($data);

    return view('macros.join.button-delete-friend', $tpl)->render();
});

/**
 * Button share on post
 */
 HTML::macro('shareBtn', function ($post, $size = null, $btnText = null, $inLi = true) {
    $tpl = [];
    $tpl['size'] = $size;
    $tpl['post'] = $post;
    $tpl['shareCount'] = $post->share;
    $tpl['inLi'] = $inLi;
    if ($btnText == null) {
        $btnText = trans("netframe.shareNetframe");
    }
    $tpl['btnText'] = $btnText;

    return view('macros.button-share', $tpl)->render();
 });

/**
 * Button share on post
 */
/*
HTML::macro('shareBtn', function($idNewsfeed, $shareCount, $size = null, $btnText = null, $inLi = false )
{
    $tpl = [];
    $tpl['size'] = $size;
    $tpl['idNewsfeed'] = $idNewsfeed;
    $tpl['shareCount'] = $shareCount;
    $tpl['inLi'] = $inLi;
    if($btnText == null){
        $btnText = trans("netframe.share");
    }
    $tpl['btnText'] = $btnText;

    return view('macros.button-share', $tpl)->render();
});
*/


/**
 * Button share profile
 */
 HTML::macro('shareBtnProfile', function ($profile, $inPost = false) {
    $tpl = [];
    $tpl['profile'] = $profile;
    $tpl['inPost'] = $inPost;

    return view('macros.button-share-profile', $tpl)->render();
 });

/**
 * Button share media
 */
 HTML::macro('shareBtnMedia', function ($media, $classCss = '', $removeTxt = false) {
    $tpl = [];
    $tpl['media'] = $media;
    $tpl['classCss'] = $classCss;
    $tpl['removeTxt'] = $removeTxt;

    return view('macros.button-share-media', $tpl)->render();
 });

/**
 * Button share playlist
 */
 HTML::macro('shareBtnPlaylist', function ($playlist, $classCss = '') {
    $tpl = [];
    $tpl['playlist'] = $playlist;
    $tpl['classCss'] = $classCss;

    return view('macros.button-share-playlist', $tpl)->render();
 });

/**
 * pin to top post button
 */
 HTML::macro('pinTop', function ($data) {
    $tpl = [];

    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['pinned'] = $data['pinned'];

    return view('macros.button-pintop', $tpl)->render();
 });

/**
 * join button
 */
 HTML::macro('joinProfileBtn', function (
     $profile_id,
     $profile_type,
     $users_id,
     $joined = null,
     $free_join = 1,
     $members = 0
 ) {
    $tpl = [];
    $tpl['profile_id']      = $profile_id;
    $tpl['profile_type']    = $profile_type;
    $tpl['users_id']        = $users_id;
    $tpl['joined']          = $joined;
    $tpl['free_join'] = $free_join;
    $tpl['dataJson'] = json_encode([
        'profile_id' => $profile_id,
        'profile_type' => $profile_type,
        'users_id' => $users_id
    ]);
    $tpl['members'] = $members;

    return view('macros.join.button-join-profile', $tpl)->render();
 });

 HTML::macro('joinAnswerBtn', function ($data, $simple = false) {
    $tpl = [];
    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['simpleButton'] = $simple;
    return view('macros.join.button-join-answer', $tpl)->render();
 });

/**
 * join button
 */
 HTML::macro('inviteAnswerBtn', function ($data) {
    $tpl = [];
    $profile_id      = $data['profile_id'];
    $profile_type    = $data['profile_type'];
    $users_id        = $data['users_id'];
    $tpl['dataJsonEncoded'] = json_encode([
        'profile_id' => $profile_id,
        'profile_type' => $profile_type,
        'users_id' => $users_id
    ]);

    return view('macros.join.button-invite-answer', $tpl)->render();
 });

/*
 * button to remove user invited
 */
 HTML::macro('removeInviteBtn', function ($data) {
    $tpl = [];
    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['user_role'] = $data['user_role'];
    return view('macros.join.button-remove-invite', $tpl)->render();
 });

/**
 *  Button contact offer
 */
 HTML::macro('contactOffer', function ($offer) {
    $tpl = [];
    $tpl['offer'] = $offer;

    return view('macros.button-contact-offer', $tpl)->render();
 });

/*
 * Button to change user role in instance
 */

 HTML::macro('instanceRoleAction', function ($data) {
    $tpl = [];
    $tpl['dataJsonEncoded'] = json_encode($data);
    $tpl['user_role'] = $data['user_role'];
    $tpl['user_id'] = $data['user_id'];
    return view('macros.button-instance-action', $tpl)->render();
 });
