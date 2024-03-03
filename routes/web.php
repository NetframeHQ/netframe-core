<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */


/*
 * ============================================================================================
 * ============================================================================================
 * ============================================================================================
 *
 *      / \
 *     / ! \            /ws  is a reserved uri for sockets proxypass
 *    /     \
 *
 * ============================================================================================
 * ============================================================================================
 * ============================================================================================
 */

#
#-----------------------------------------FOR APP-----------------------------------------------
#
Route::get('uid-token/{duuid}/{fcmToken}', [
    'as' => 'app.device.fcm',
    'uses' => 'AppController@deviceToken',
]);

#
#-----------------------------------------EXTERNAL VISIO-----------------------------------------------
#

Route::match(['GET', 'POST'], 'gotovisio/{instanceId}/{channelId}/{slug}', [
    'as' => 'external.visio.access',
    'uses' => 'PublicController@externalVisio',
]);

#
#-----------------------------------------INSTANCES IMAGES-----------------------------------------------
#

Route::get('download/{parametername}/{filename?}', [
    'prefix' => 'instance',
    'as' => 'instance.download',
    'uses' => 'Instance\GraphicalController@download'
]);
#
#-----------------------------------------BOARDING PAGES-----------------------------------------------
#
Route::get('boarding/confirm/{type}', [
    'as' => 'boarding.confirm.creation',
    'uses' => 'BoardingController@confirmAccountCreated',
]);

Route::group(['prefix' => 'boarding', 'middleware' => 'userHome'], function () {
    Route::get('resent-link', [
        'as' => 'boarding.resent.link',
        'uses' => 'BoardingController@resentLink',
    ]);

    Route::get('/', [
        'as' => 'boarding.home',
        'uses' => 'BoardingController@home',
    ]);

    Route::get('key/{boardingKey}/{encodedEmail?}', [
        'as' => 'boarding.bykey',
        'uses' => 'BoardingController@byKey',
    ]);

    Route::match(['get', 'post'], 'join-instance', [
        'as' => 'boarding.attach.instance',
        'uses' => 'BoardingController@attachInstance'
    ]);

    Route::get('new-code/{boardingId}', [
        'as' => 'boarding.newcode',
        'uses' => 'BoardingController@newCode',
    ])->where('boardingId', '\d+');

    Route::post('send-code', [
        'as' => 'boarding.sendcode',
        'uses' => 'BoardingController@sendCode',
    ]);

    Route::match(['GET', 'POST'], 'checkin', [
        'as' => 'boarding.checkcode',
        'uses' => 'BoardingController@checkCode',
    ]);

    Route::group(['prefix' => 'instance', 'middleware' => 'boarding'], function () {
        Route::match(['GET', 'POST'], '/', [
            'as' => 'boarding.create.instance',
            'uses' => 'BoardingController@createInstance',
        ]);
    });
});

#
#-----------------------------------------BOARDING PAGES NEW USER-----------------------------------------------
#

Route::group(['namespace' => 'User', 'prefix' => 'welcome', 'middleware' => 'checkAuth'], function () {
    Route::get('/subscription', [
        'as' => 'boarding.admin.step1',
        'uses' => 'BoardingController@adminBoarding1',
    ]);

    Route::match(['get', 'post'], '/payment-infos', [
        'as' => 'boarding.admin.stepCB',
        'uses' => 'BoardingController@adminBoardingCB',
    ]);

    Route::get('/boarding', [
        'as' => 'boarding.admin.step2',
        'uses' => 'BoardingController@adminBoarding2',
    ]);

    Route::get('/themes', [
        'as' => 'boarding.admin.step3',
        'uses' => 'BoardingController@adminBoarding3',
    ]);

    Route::get('/groups', [
        'as' => 'boarding.admin.step4',
        'uses' => 'BoardingController@adminBoarding4',
    ]);

    Route::get('/new-user', [
        'as' => 'boarding.user.step1',
        'uses' => 'BoardingController@userBoarding1',
    ]);

    Route::get('/contacts-groups', [
        'as' => 'boarding.user.step2',
        'uses' => 'BoardingController@userBoarding2',
    ]);

    Route::get('/post', [
        'as' => 'boarding.user.step3',
        'uses' => 'BoardingController@userBoarding3',
    ]);

    Route::group(['prefix' => 'modals'], function () {
        Route::get('welcome', [
            'as' => 'welcome.modal.welcome',
            'uses' => 'BoardingController@modalWelcome'
        ]);

        Route::get('create-group', [
            'as' => 'welcome.modal.create.group',
            'uses' => 'BoardingController@modalGroup'
        ]);

        Route::post('call-back', [
            'as' => 'welcome.modal.call.back',
            'uses' => 'BoardingController@modalCallBack'
        ]);

        Route::get('invit-users', [
            'as' => 'welcome.modal.invite.users',
            'uses' => 'BoardingController@modalInvite'
        ]);
    });

    Route::get('accept-charter', [
        'as' => 'boarding.user.accept.charter',
        'uses' => 'BoardingController@acceptCharter',
    ]);
});

#
#-----------------------------------------STATIC PAGES-----------------------------------------------
#
Route::group(['prefix' => 'static'], function () {
    Route::get('instance-closed', [
        'as' => 'instance_closed',
        'uses' => 'PageController@instanceClosed'
    ]);

    Route::get('cgu', [
        'as' => 'static_cgu',
        'uses' => 'PageController@cgu'
    ]);

    Route::get('cgv', [
        'as' => 'static_cgv',
        'uses' => 'PageController@cgv'
    ]);

    Route::get('faq', [
        'as' => 'static_faq',
        'uses' => 'PageController@faq'
    ]);

    Route::get('contacts', [
        'as' => 'static_contacts',
        'uses' => 'PageController@contacts'
    ]);
});

#
#-----------------------------------------AUTHENTICATION-----------------------------------------------
#
Route::group(['prefix' => '/'], function () {
    Route::match(['get', 'post'], 'register/{messageRegister?}', [
        //'middleware' => ['userHome', 'boardingInstance'],
        'as' => 'auth.register',
        'uses' => 'AuthController@register'
    ]);

    Route::match(['get', 'post'], 'login/{messageLogin?}', [
        'as' => 'auth.login',
        'middleware' => 'userHome',
        'uses' => 'AuthController@login'
    ]);

    Route::match(['get', 'post'], 'login/{messageLogin?}', [
        'as' => 'login',
        'middleware' => 'userHome',
        'uses' => 'AuthController@login'
    ]);

    Route::get('logout', [
        'as' => 'auth.logout',
        'uses'=>'AuthController@logout'
    ]);

    Route::match(['get', 'post'], 'forgot-password', [
        'middleware' => 'userHome',
        'as' => 'auth.forgotPassword',
        'uses' => 'AuthController@forgotPassword'
    ]);

    Route::match(['get', 'post'], 'remind-password/{token?}', [
        'middleware' => 'userHome',
        'as' => 'auth.remindPassword',
        'uses' => 'AuthController@remindPassword'
    ]);
});

#
#---------------------------MEDIA TOTAL SIZE FROM ENCODER SERVER---------------------------------
#
Route::get('media/compute-size/{mediaId}', [
    'middleware' => 'restrictedIp',
    'as' => '',
    'uses' => 'User\MediaController@computeSize'
])->where('mediaId', '\d+');


Route::group(['prefix' => '/', 'middleware' => 'checkAuth'], function () {
    // include instance routes
    Route::group([], __DIR__.'/instance.php');

    #
    #-----------------------------------------VISIO-----------------------------------------------
    #
    Route::group(['prefix' => 'visio', 'namespace' => 'Channel'], function () {
        Route::get('manage-links/{channelId}', [
            'as' => 'visio.manage.link',
            'uses' => 'VisioController@manageLink',
        ]);

        Route::post('add-visio-access', [
            'as' => 'visio.link.add',
            'uses' => 'VisioController@addLink',
        ]);

        Route::get('delete-visio-access/{channel_id}/{access_id}', [
            'as' => 'visio.link.delete',
            'uses' => 'VisioController@deleteLink',
        ]);
    });

    #
    #-----------------------------------------CHANNELS-----------------------------------------------
    #
    Route::group(['prefix' => 'channels', 'namespace' => 'Channel', 'middleware' => 'activeChannels'], function () {
        Route::get('main', [
            'as' => 'channels.main',
            'uses' => 'ChannelController@main',
        ]);

        Route::get('/', [
            'as' => 'channels.feeds',
            'uses' => 'ChannelController@feeds',
        ]);

        Route::get('my-feeds', [
            'as' => 'channels.my.feeds',
            'uses' => 'ChannelController@myFeeds',
        ]);

        Route::match(['GET', 'POST'], 'edit/{id?}', [
            'as' => 'channel.edit',
            'uses' => 'ChannelController@edit',
        ])->where('id', '\d+');

        Route::match(['GET'], 'delete/{id}', [
            'as' => 'channels.delete',
            'uses' => 'ChannelController@delete',
        ])->where('id', '\d+');

        Route::match(['GET'], 'disable/{id}/{active}', [
            'as' => 'channels.disable',
            'uses' => 'ChannelController@disable',
        ])->where('id', '\d+')
          ->where('active', '0|1');

        Route::post('unread/{id?}', [
            'as' => 'channels.unread',
            'uses' => 'ChannelController@unread',
        ])->where('id', '\d+');

        Route::get('emojis', [
            'as' => 'channels.emojis',
            'uses' => 'MessagesController@emojis',
        ]);

        Route::match(array('GET', 'POST'), '{id?}/channel/{status}', [
            'as' => 'channel_edit_community',
            'uses' => 'ChannelController@editCommunity'
        ])->where('id', '\d+')
        ->where('status', '\d+');

        Route::get('{id}/invite/', [
            'as' => 'channel_invite',
            'uses' => 'ChannelController@inviteUsers'
        ])->where('id', '\d+');

        Route::get('{id}/post/{postId}', [
            'as' => 'channels.getpost',
            'uses' => 'MessagesController@getPost',
        ])->where('id', '\d+')
        ->where('postId', '\d+');

        Route::match(['GET', 'POST'], '{id}/{when?}/{datetime?}', [
            'as' => 'channels.home',
            'uses' => 'ChannelController@feed',
        ])->where('id', '\d+');

        Route::get('mark-read/{feedId}', [
            'as' => 'channels.mark.read',
            'uses' => 'ChannelController@markRead',
        ])->where('feedId', '\d+');

        Route::match(['GET', 'POST'], 'post/{id?}/{channelId?}', [
            'as' => 'channel.post',
            'uses' => 'MessagesController@posting',
        ])->where('id', '\d+')
          ->where('channelId', '\d+');

        Route::match(['GET', 'POST'], 'delete-post/{id?}', [
            'as' => 'channel.post.delete',
            'uses' => 'MessagesController@delete',
        ])->where('id', '\d+');

        Route::get('messenger/{userId}', [
            'as' => 'channels.messenger',
            'uses' => 'ChannelController@messenger',
        ])->where('userId', '\d+');

        Route::post('search-contacts', [
            'as' => 'channels.contacts.search',
            'uses' => 'ChannelController@searchContacts',
        ]);

        Route::post('live-chat/update-members', [
            'as' => 'channels.livechat.update.members',
            'uses' => 'ChannelController@livechatMembers',
        ]);

        Route::get('live-chat/{channelId}/{form?}/{fromUser?}', [
            'as' => 'channels.livechat',
            'uses' => 'ChannelController@livechat',
        ])->where('channelId', '\d+');
    });

    #
    #-----------------------------------------CALENDAR-----------------------------------------------
    #

    Route::get('calendar/export', [
        'as' => 'calendar.export',
        'uses' => 'User\CalendarController@export',
    ]);

    Route::get('calendar/import', [
        'as' => 'calendar.import',
        'uses' => 'User\CalendarController@import',
    ]);

    Route::get('calendar/authorize/{type}', [
        'as' => 'calendar.authorize',
        'uses' => 'User\CalendarController@calendarAuthorize',
    ]);

    Route::match(['GET', 'POST'], 'calendar/synchronize/{event_id?}', [
        'as' => 'calendar.synchronize',
        'uses' => 'User\CalendarController@synchronize'
    ])
    ->where([
        'event_id'=> '\d+',
    ]);

    Route::get('calendar/{profile_type?}/{profile_id?}', [
        'as' => 'calendar.home',
        'uses' => 'User\CalendarController@home',
    ]);

    Route::get('calendardates/{type}/{profile_type?}/{profile_id?}', [
        'as' => 'calendar.dates',
        'uses' => 'User\CalendarController@loadDates',
    ]);


    Route::get('calendar/launch-export/{id}/{email}', [
        'as' => 'calendar.launchExport',
        'uses' => 'User\CalendarController@launchExport',
    ]);

    /*
    Route::get('calendar/authorize/{type}', [
        'as' => 'calendar.authorize',
        'uses' => 'User\CalendarController@calendarAuthorize',
    ]);
    */

    #-----------------------------------------HOME-----------------------------------------------
    #
    Route::get('workspace', [
        'as' => 'home',
        'uses' => 'NetframeController@workspaceHome'
    ]);

    Route::get('portal', [
        'as' => 'portal',
        'uses' => 'NetframeController@portal'
    ]);

    Route::get('/', function () {
        return redirect()->route('netframe.workspace.home');
    });

    #
    #-----------------------------------------TAGS-----------------------------------------------
    #

    Route::group(array('prefix' => 'tags'), function () {
        Route::get('page/{tagId}/{tagName}', array(
            'as' => 'tags.page',
            'uses' => 'TagController@page'
        ))->where('tagId', '\d+');

        Route::post('/autocomplete', [
            'as' => 'tags.autocomplete',
            'uses' => 'TagController@autocomplete'
        ]);
    });


    #-----------------------------------------MEDIAS-----------------------------------------------
    #

    Route::post('/social/media/{mediaId}', [
        'as' => 'social.media',
        'uses' => 'User\MediaController@socialToolbar'
    ])->where(['mediaId' => '[0-9]+']);

    Route::post('/action-menu', [
        'as' => 'media.actions.menu',
        'uses' => 'User\MediaController@actionsMenu'
    ])->where(['mediaId' => '[0-9]+']);

    Route::post('/social/media/comments', [
        'as' => 'more.comments',
        'uses' => 'User\MediaController@showMoreComments'
    ]);

    Route::get('/media/{fileName}/{mediaId}', [
        'as' => 'urlto.media',
        'uses' => 'User\MediaController@show'
    ])->where(['mediaId' => '[0-9]+']);

    // call profile comments modal
    Route::get('/comments/media/{mediaId}/{take}', [
        'as' => 'media.comments',
        'uses' => 'User\MediaController@mediaComments'
    ])->where('mediaId', '\d+')
    ->where('take', '\w+');

    Route::get('medias/reach-quota', [
        'as' => 'media.quota.reach',
        'uses' => 'User\MediaController@quotaReach'
    ]);

    // modal media view with id media and collection medias
    Route::post('media-player', [
        'as' => 'modal.media.player',
        'uses' => 'User\MediaController@modalMedia'
    ]);

    #
    #-----------------------------------------LINK PREVIEW-----------------------------------------------
    #
    Route::group(array('prefix' => 'link-preview'), function () {
        Route::post('get-metas', [
            'as' => 'link.metas',
            'uses' => 'LinkPreviewController@getMetas',
        ]);

        Route::get('download/{id}', [
            'as' => 'link.download',
            'uses' => 'LinkPreviewController@download',
        ]);
    });

    #
    #-----------------------------------------USERS-----------------------------------------------
    #

    Route::group(['namespace' => 'User', 'prefix' => 'user', 'middleWare' => 'checkAuth',], function () {
        Route::get('{slug}/{fullname}/{idNewsFeed?}', [
            'as' => 'user.wall',
            'uses' => 'ProfileController@wall'
        ])->where(['idNewsFeed' => '[0-9]+']);

        Route::get('timeline', [
            'as' => 'user.timeline',
            'uses' => 'ProfileController@timeline'
        ]);
    });

    Route::group(['namespace' => 'User', 'prefix' => 'medias', 'middleWare' => 'checkAuth',], function () {
        Route::post('switch-view', [
            'as' => 'medias_explorer.switch_view',
            'uses' => 'MediaController@switchView',
        ]);

        Route::get('general', [
            'as' => 'medias_explorer-general',
            'uses' => 'MediaController@general',
        ]);

        Route::match(['GET', 'POST'], 'show/{profileType}/{profileId}/{folder?}/{driveFolder?}', [
            'as' => 'medias_explorer',
            'uses' => 'MediaController@showList'
        ])->where('profileType', 'user|house|community|project|channel')
          ->where('profleId', '\d+')
          ->where('folder', '\d+|channels')
          ->where('driveFolder', '(.*)');

        Route::match(['GET', 'POST'], 'edit-folder/{profileType}/{profileId}/{idFolder?}/{driveFolder?}', [
            'as' => 'xplorer_edit_folder',
            'uses' => 'MediaController@editFolder'
        ])->where('profileType', 'user|house|community|project')
          ->where('profleId', '\d+')
          ->where('idFolder', '\d+')
          ->where('driveFolder', '(.*)');

        Route::match(['GET', 'POST'], 'add-file/{profileType}/{profileId}/{idFolder?}/{driveFolder?}', [
            'as' => 'xplorer_add_file',
            'uses' => 'MediaController@addFile'
        ])->where('profileType', 'user|house|community|project')
          ->where('profleId', '\d+')
          ->where('idFolder', '\d+')
          ->where('driveFolder', '(.*)');

        Route::match(['GET', 'POST'], 'edit-file/{idFile?}', [
            'as' => 'xplorer_edit_file',
            'uses' => 'MediaController@editFile'
        ])->where('idFile', '\d+');

        Route::post('drag-element', [
            'as' => 'xplorer_drag_element',
            'uses' => 'MediaController@dragElement'
        ]);

        Route::match(['GET', 'POST'], 'move-element/{profileType?}/{profileId?}/{elementType?}/{elementId?}', [
            'as' => 'xplorer_move_element',
            'uses' => 'MediaController@moveElement'
        ])->where('profileType', 'user|house|community|project')
          ->where('profleId', '\d+')
          ->where('elementType', 'media|folder')
          ->where('elementId', '\d+');

        Route::match(['GET', 'POST'], 'copy-element/{profileType?}/{profileId?}/{elementType?}/{elementId?}', [
          'as' => 'xplorer_copy_element',
          'uses' => 'MediaController@copyElement'
        ])->where('profileType', 'user|house|community|project')
          ->where('profleId', '\d+')
          ->where('elementType', 'media|folder')
          ->where('elementId', '\d+');

        Route::post('load-profile-folders', [
            'as' => 'xplorer.load.profile.folders',
            'uses' => 'MediaController@loadFolders',
        ]);

        Route::post('delete', [
            'as' => 'xplorer_delete_element',
            'uses' => 'MediaController@delete'
        ]);

        Route::post('star-media', [
            'as' => 'xplorer_star_media',
            'uses' => 'MediaController@starMedia',
        ]);

        Route::post('modify-lock', [
            'as' => 'xplorer_modify_lock',
            'uses' => 'MediaController@modifyLock'
        ]);

        Route::post('test-file-folder', [
            'as' => 'xplorer.file.in.folder',
            'uses' => 'MediaController@testFileFolder',
        ]);

        Route::get('archives/{mediaId}', [
            'as' => 'xplorer.media.archives',
            'uses' => 'MediaController@viewArchives'
        ]);

        Route::get('download/archive/{id}', [
            'as' => 'media.download.archive',
            'uses' => 'MediaController@downloadArchive'
        ]);

        Route::get('details/{id}', [
            'as' => 'media.details',
            'uses' => 'MediaController@details'
        ]);
    });


    Route::group(['namespace' => 'User', 'prefix' => 'account', 'middleware' => 'checkAuth'], function () {
        Route::match(['GET', 'POST'], 'details', [
            'as' => 'account.account',
            'uses' => 'AccountController@account'
        ]);

        Route::match(['GET', 'POST'], 'edit-password', [
            'as' => 'account.editPassword',
            'uses' => 'AccountController@editPassword'
        ]);

        Route::match(['GET', 'POST'], 'more', [
            'as' => 'account.more',
            'uses' => 'AccountController@more'
        ]);

        Route::match(['GET', 'POST'], 'calendars', [
            'as' => 'account.calendars',
            'uses' => 'AccountController@calendars'
        ]);

        Route::match(['GET', 'POST'], 'privacy', [
            'as' => 'account.privacy',
            'uses' => 'AccountController@privacy'
        ]);

        Route::match(['GET', 'POST'], 'notifications', [
            'as' => 'account.notifications',
            'uses' => 'AccountController@notifications'
        ]);

        Route::post('reference/add', [
            'as' => 'user.reference.add',
            'uses' => 'ProfileController@addReference'
        ]);

        Route::get('reference/delete/{id}', [
            'as' => 'user.reference.delete',
            'uses' => 'ProfileController@deleteReference'
        ])->where('id', '\d+');

        Route::post('reference/valid/{id}', [
            'as' => 'user.reference.valid',
            'uses' => 'ProfileController@validReference'
        ])->where('id', '\d+');
    });


    Route::group(['namespace' => 'User', 'middleware' => 'checkAuth', 'prefix' => 'interests'], function () {
        Route::match(['GET', 'POST'], 'settings/{id?}', [
            'as' => 'intersets_settings',
            'uses' => 'InterestController@settings'
        ])->where('id', '\d+');

        Route::match(['GET', 'POST'], 'settings/{id?}/delete', [
            'as' => 'intersets_settings_delete',
            'uses' => 'InterestController@delete'
        ])->where('id', '\d+');
    });

    #
    #-----------------------------------------MESSAGES-----------------------------------------------
    #

    Route::group(array('prefix' => 'messages', 'middleware' => 'checkAuth'), function () {
        Route::get('form-message/{toType}/{toId}/{fromType?}/{fromId?}/{type?}', [
            'as' => 'messages_form_message',
            'uses' => 'MessageMailController@getFormMessage'
        ]);

        Route::post('message-post', [
            'as' => 'messages_form_message_post',
            'uses' => 'MessageMailController@postMessagePost'
        ]);

        Route::get('inbox', [
            'as' => 'messages_inbox',
            'uses' => 'MessageMailController@inbox'
        ]);

        Route::post('infinitebox', [
            'as' => 'messages_infinitebox',
            'uses' => 'MessageMailController@infinitebox'
        ]);

        Route::get('/outbox', [
            'as' => 'messages_outbox',
            'uses' => 'MessageMailController@outbox'
        ]);

        Route::get('feed/{feedId}/{lastId?}', [
            'as' => 'messages_feed',
            'uses' => 'MessageMailController@getAllFeed'
        ])->where('feedId', '\d+');

        Route::get('all-read', [
            'as' => 'messages_all_read',
            'uses' => 'MessageMailController@markAllRead'
        ]);

        Route::get('new', [
            'as' => 'new_messages',
            'uses' => 'MessageMailController@newMessage'
        ]);
    });

    #
    #-----------------------------------------OFFERS-----------------------------------------------
    #

    Route::group(['namespace' => 'User', 'prefix' => 'offer', 'middleware' => 'checkAuth'], function () {
        Route::match(['GET', 'POST'], '{profileType}/{profileId}/{id?}', [
            'as' => 'offer_edit',
            'uses' => 'OfferController@editOffer'
        ])->where('id', '\d+')
          //->where('profileType', 'house')
          ->where('profileId', '\d+');

        Route::post('search', [
            'as' => 'search_offers',
            'uses' => 'OfferController@searchOffer'
        ]);

        Route::get('marketplace', [
            'as' => 'offers_marketplace',
            'uses' => 'OfferController@marketplace'
        ]);

        Route::post('skills/{refName}/{offerType}', [
            'as' => 'skills_offers',
            'uses' => 'OfferController@loadFormSkills'
        ])->where('refName', 'mp_search_name|mp_proposal_name')
        ->where('offerType', 'offer|demand|0');
    });


    #
    #-----------------------------------------PROJECTS-----------------------------------------------
    #
    Route::group(['namespace' => 'User', 'prefix' => 'project', 'middleware' => 'checkAuth'], function () {
        Route::match(array('GET', 'POST'), 'edit/{id?}', [
            'as' => 'project.edit',
            'uses' => 'ProjectController@edit'
        ])->where('id', '\d+');

        Route::get('manage', [
            'as' => 'project.manage',
            'uses' => 'ProjectController@manage'
        ]);

        Route::match(array('GET', 'POST'), '{id?}/bookmarks', [
            'as' => 'project_edit_bookmarks',
            'uses' => 'ProjectController@editBookmarks'
        ])->where('id', '\d+');

        Route::match(array('GET', 'POST'), 'bookmark_form/{idProject}/{idBookmark?}', [
            'as' => 'project_bookmark_form',
            'uses' => 'ProjectController@bookmarkForm'
        ])->where('idProject', '\d+')
        ->where('idBookmark', '\d+');

        Route::get('bookmark_delete/{idProject}/{idBookmark?}', [
            'as' => 'project_bookmark_delete',
            'uses' => 'ProjectController@bookmarkDelete'
        ])->where('idProject', '\d+')
        ->where('idBookmark', '\d+');

        Route::get('inbox/{idProject}/{full?}', [
            'as' => 'project.inbox',
            'uses' => 'ProjectController@inbox'
        ])->where('idProject', '\d+');

        Route::match(array('GET', 'POST'), '{id?}/community/{status}', [
            'as' => 'project_edit_community',
            'uses' => 'ProjectController@editCommunity'
        ])->where('id', '\d+')
        ->where('status', '\d+');

        Route::get('{id}/invite/', [
            'as' => 'project_invite',
            'uses' => 'ProjectController@inviteUsers'
        ])->where('id', '\d+');
    });

    #
    #-----------------------------------------community-----------------------------------------------
    #

    Route::group(['namespace' => 'User', 'prefix' => 'community', 'middleware' => 'checkAuth'], function () {
        Route::get('community', [
            'as' => 'community',
            'uses' => 'CommunityController@liste']);

        Route::match(['GET', 'POST'], 'edit/{id?}', [
            'as' => 'community.edit',
            'uses' => 'CommunityController@edit'
        ])->where('id', '[0-9]+');

        Route::get('manage', [
            'as' => 'community.manage',
            'uses' => 'CommunityController@manage'
        ]);

        Route::get('inbox/{idCommunity}/{full?}', [
            'middleware' => 'checkAuth',
            'as' => 'community.inbox',
            'uses' => 'CommunityController@inbox'
        ])->where('idCommunity', '\d+');

        Route::match(array('GET', 'POST'), '{id?}/community/{status}', [
            'as' => 'community_edit_community',
            'uses' => 'CommunityController@editCommunity'
        ])->where('id', '\d+')
        ->where('status', '\d+');

        Route::get('{id}/invite/', [
            'as' => 'community_invite',
            'uses' => 'CommunityController@inviteUsers'
        ])->where('id', '\d+');
    });

    Route::group(['namespace' => 'User', 'prefix' => 'house', 'middleware' => 'checkAuth'], function () {
        Route::match(['GET', 'POST'], 'edit/{id?}', [
            'as' => 'house.edit',
            'uses' => 'HouseController@edit'
        ])->where('id', '\d+');

        Route::get('manage', [
            'as' => 'house.manage',
            'uses' => 'HouseController@manage'
        ]);

        Route::match(array('GET', 'POST'), '{id?}/community/{status}', [
            'as' => 'house_edit_community',
            'uses' => 'HouseController@editCommunity'
        ])->where('id', '\d+')
        ->where('status', '\d+');

        Route::get('{id}/invite/', [
            'as' => 'house_invite',
            'uses' => 'HouseController@inviteUsers'
        ])->where('id', '\d+');
    });

    #
    #-----------------------------------------PAGES-----------------------------------------------
    #
    Route::group([
      'namespace' => 'User',
      'middleware' => ['checkAuth', 'profileNavigate'],
      'prefix' => 'page'
    ], function () {
        Route::get("house/{id}/{name}/{idNewsFeed?}", [
            'as' => 'page.house',
            'uses' => 'PageController@house'
        ])->where([
            'id' => '[0-9]+',
            'name' => '[a-z0-9-]+',
            'idPost' => '[0-9]+'
        ]);

        Route::get("community/{id}/{name}/{idNewsFeed?}", [
            'as' => 'page.community',
            'uses' => 'PageController@community'
        ])->where([
            'id' => '[0-9]+',
            'name' => '[a-z0-9-]+',
            'idPost' => '[0-9]+'
        ]);

        Route::get("project/{id}/{name}/{idNewsFeed?}", [
            'as' => 'page.project',
            'uses' => 'PageController@project'
        ])->where([
            'id' => '[0-9]+',
            'name' => '[a-z0-9-]+',
            'idPost' => '[0-9]+'
        ]);

        // Map location
        Route::get('map/', [
            'as' => 'profile.map.location',
            'uses' => 'PageController@location'
        ]);

        // Map location
        Route::post('map/card/{profileId}/{profileType}', [
            'as' => 'profile.map.card',
            'uses' => 'PageController@mapCard'
        ])->where([
            'profile' => '[0-9]+',
            'profileType' => '[a-z]+',
        ]);

        // Modal identity card
        Route::get('identity-card/{profil}/{id}/p-{prevId?}/n-{nextId?}/{prevProfile?}/{nextProfile?}', [
            'as' => 'identity.card',
            'uses' => 'PageController@identityCard'
        ])->where([
            'id' => '[0-9]+',
            'profil' => '[a-z0-9-]+',
            'nextId' => '[0-9]+',
            'prevId' => '[0-9]+'
        ]);

        //mini post view in modal with all comments
        Route::get('post/{id}/{modal?}', [
            'as' => 'post.modal',
            'uses' => 'PageController@unitPostModal'
        ])->where([
            'id' => '[0-9]+'
        ]);

        //all comments sub view
        Route::post('post/all-comments', [
            'as' => 'post.all.comments',
            'uses' => 'PageController@allComments'
        ]);

        // retrieve sidebar widget content in modal for user
        Route::get('sidebar/{id}/{typeContent}', [
            'as' => 'sidebar.user.widget',
            'uses' => 'ProfileController@sideBarContent'
        ])->where([
            'id' => '[0-9]+',
        ]);

        // retrieve sidebar widget content in modal for profiles
        Route::get('sidebar/{profile}/{id}/{typeContent}', [
            'as' => 'sidebar.profile.widget',
            'uses' => 'PageController@sideBarContent'
        ])->where([
            'id' => '[0-9]+',
        ]);

        // call profile comments modal
        Route::get('/comments/{profileType}/{profileId}/{take}', [
            'as' => 'profile.comments',
            'uses' => 'PageController@profileComments'
        ])->where('profileType', '\w+')
        ->where('profileId', '\d+')
        ->where('take', '\w+');

        // disable a profile
        Route::get('/disable/{profileType}/{profileId}/{active}', [
            'as' => 'profile.disable',
            'uses' => 'PageController@disableProfile'
        ])->where('profileId', '\d+')
        ->where('profileType', 'community|house|project')
        ->where('active', '0|1');

        // stats for groups
        Route::get('/stats/{profileType}/{profileId}/{period?}', [
            'as' => 'profile.stats',
            'uses' => 'PageController@stats'
        ])->where('profileId', '\d+')
        ->where('profileType', 'community|house|project')
        ->where('period', '\d+');
    });


    #
    #-----------------------------------------PLAYLISTS-----------------------------------------------
    #
    Route::group(['namespace' => 'User', 'prefix' => 'playlist', 'middleware' => 'checkAuth'], function () {

        Route::get('{id}', [
            'as' => 'playlist_show',
            'uses' => 'PlaylistController@show'
        ])->where(['id' => 'instant|\d+']);

        Route::post('instant-bookmark/profile/{profileType}/{profileId}', [
            'as' => 'playlist_user_profile_bookmark',
            'uses' => 'PlaylistController@instantBookmarkProfileAsUser'
        ])  ->where('profileType', '\w+')
            ->where('profileId', '\d+');

        Route::post('instant-bookmark/media/{profileType}/{profileId}/{mediaId}', [
            'as' => 'playlist_user_profile_media_bookmark',
            'uses' => 'PlaylistController@instantBookmarkProfileMediaAsUser'
        ])  ->where('profileType', '\w+')
            ->where('profileId', '\d+')
            ->where('mediaId', '\d+');

        Route::get('/playlist/item/{id}/delete', [
            'as' => 'playlist_item_delete',
            'uses' => 'PlaylistController@deleteItem'
        ])->where('id', '\d+');

        Route::post('/', [
            'as' => 'playlist_create',
            'uses' => 'PlaylistController@create'
        ]);

        Route::get('{id}/item/{itemId}', [
            'as' => 'playlist_item_add',
            'uses' => 'PlaylistController@addItem'
        ]);

        Route::get('delete/{id}', [
            'as' => 'playlist_delete',
            'uses' => 'PlaylistController@delete'
        ]);

        Route::match(array('GET', 'POST'), 'add', [
            'as' => 'playlist_add',
            'uses' => 'PlaylistController@edit'
        ]);
    });

    #
    #-----------------------------------------EVENTS-----------------------------------------------
    #

    Route::group(['namespace' => 'User', 'prefix' => 'event', 'middleware' => 'checkAuth'], function () {
        Route::match(array('GET', 'POST'), 'event/edit/{id?}', [
            'as' => 'event_edit',
            'uses' => 'EventController@edit'
        ])->where(['id'=>'\d+']);

        Route::post('/participate/{eventId}', [
            'as' => 'event.participate',
            'uses' => 'EventController@participate'
        ])->where(['eventId'=>'\d+']);

        Route::get('/participants/{eventId}', [
            'as' => 'event.participants',
            'uses' => 'EventController@participants'
        ])->where(['eventId'=>'\d+']);

        Route::match(array('GET', 'POST'), 'all', [
            'as' => 'event_dashboard',
            'uses' => 'EventController@dashboard'
        ]);

        Route::post('search', [
            'as' => 'search_events',
            'uses' => 'EventController@search'
        ]);
    });

    #
    #-----------------------------------------SEARCH-----------------------------------------------
    #

    Route::match(array('GET'), 'search', [
        'middleware' => 'checkAuth',
        'as' => 'search',
        'uses' => 'Search'
    ]);


    #
    #-----------------------------------------JOIN-----------------------------------------------
    #

    Route::group(['namespace' => 'User', 'prefix' => 'join', 'middleware' => 'checkAuth'], function () {
        Route::get('form/{profile_id}/{profile_type}/{users_id}', [
            'as' => 'join.ask',
            'uses' => 'JoinController@getFormJoinAsk'
        ])
        ->where([
            'profile_id'    => '\d+',
            'profile_type'  => '[a-z]+',
            'users_id'      => '\d+'
        ]);

        Route::post('post', [
            'as' => 'join.ask.post',
            'uses' => 'JoinController@postJoinAsk'
        ]);

        Route::post('answer/{action}', [
            'as' => 'join.answer',
            'uses' => 'JoinController@joinAnswer'
        ]);

        Route::post('remove', [
            'as' => 'join.remove',
            'uses' => 'JoinController@removeJoin'
        ]);

        Route::post('search-users', [
            'as' => 'join.search.users',
            'uses' => 'JoinController@searchUsers'
        ]);

        Route::post('invite-answer/{action}', [
            'as' => 'join.invite.answer',
            'uses' => 'JoinController@inviteAnswer'
        ]);

        Route::post('change-rights', [
            'as' => 'join.change.rights',
            'uses' => 'JoinController@changeRights'
        ]);
    });


    #
    #-----------------------------------------NETFRAME-----------------------------------------------
    #

    Route::group(['prefix' => 'netframe', 'middleware' => 'checkAuth'], function () {
        Route::get('home', [
            'as' => 'netframe.workspace.home',
            'uses' => 'NetframeController@workspaceHome',
        ]);

        Route::get('svg-icon/{name}', [
            'as' => 'netframe.svgicon',
            'uses' => 'NetframeController@loadSvg',
        ]);

        Route::post('sidebar-toggle', [
            'as' => 'sidebar.toggle',
            'uses' => 'NetframeController@postSidebarToggle',
        ]);

        Route::post('set-gmt', [
            'as' => 'netframe.setgmt',
            'uses' => 'NetframeController@postSetGmt',
        ]);

        Route::post('set-geolocation', [
            'as' => 'netframe.setgeoloc',
            'uses' => 'NetframeController@postSetGeolocation',
        ]);

        Route::get('big-map-json', [
            'as' => 'netframe.map',
            'uses' => 'NetframeController@getBigMapJson',
        ]);

        Route::get('likers/{elementType}/{elementId}', [
            'as' => 'post.likers',
            'uses' => 'NetframeController@likers',
        ]);

        Route::get('viewers/{elementType}/{elementId}', [
            'as' => 'post.viewers',
            'uses' => 'NetframeController@viewers',
        ]);

        Route::get('tag-people', [
            'as' => 'tag.people',
            'uses' => 'NetframeController@tagPeople',
        ]);

        Route::post('pintop', [
            'as' => 'netframe.pintop',
            'uses' => 'NetframeController@postPintop',
        ]);

        Route::match(['GET', 'POST'], 'news/{date_time_last?}', [
            'as' => 'netframe.anynews',
            'uses' => 'NetframeController@anyNews',
        ]);

        Route::get('report-abuse/{authorId?}/{postId?}/{postType?}', [
            'uses' => 'NetframeController@getReportAbuse',
        ]);

        Route::post('report-abuse/{authorId?}/{postId?}/{postType?}', [
            'uses' => 'NetframeController@postReportAbuse',
        ]);

        Route::get('form-comment-publish/{typeElement}/{idElement}/{replyTo}', [
            'as' => 'netframe.form-comment',
            'uses' => 'NetframeController@getFormCommentPublish',
        ])->where(['idElement'=> '\d+']);

        Route::get('edit-comment/{idComment}', [
            'uses' => 'NetframeController@getEditComment',
        ])->where(['idComment'=> '\d+']);

        Route::get('delete-comment/{idComment}', [
            'as' => 'netframe.delete.comment',
            'uses' => 'NetframeController@getDeleteComment',
        ])->where(['idComment'=> '\d+']);

        Route::post('comment-publish', [
            'uses' => 'NetframeController@postCommentPublish',
        ])->where(['idNewsFeed'=> '\d+']);

        Route::get('form-comment-profile/{profileType}/{idProfile}', [
            'uses' => 'NetframeController@getFormCommentProfile',
        ])->where(['idProfile'=> '\d+']);

        Route::post('subscrib-profile', [
            'uses' => 'NetframeController@postSubscribProfile',
        ]);

        Route::post('like', [
            'uses' => 'NetframeController@postLike',
        ]);

        Route::post('like-profile', [
            'uses' => 'NetframeController@postLikeProfile',
        ]);

        Route::get('form-share/{idNewsFeed}/{idShare?}', [
            'as' => 'form.share',
            'uses' => 'NetframeController@getFormShare',
        ])->where(['idNewsFeed'=> '\d+'])
          ->where(['idShare'=> '\d+']);

        Route::post('publish-share', [
            'uses' => 'NetframeController@postPublishShare',
        ]);

        Route::get('form-share-profile/{typeProfile}/{idProfile}/{id?}', [
            'as' => 'form.share.profile',
            'uses' => 'NetframeController@getFormShareProfile',
        ])->where(['idProfile'=> '\d+'])
          ->where(['id'=> '\d+']);

        Route::post('publish-share-profile', [
            'uses' => 'NetframeController@postPublishShareProfile',
        ]);

        Route::get('delete-publish/{idNewsFeed}', [
            'uses' => 'NetframeController@getDeletePublish',
        ])->where(['idNewsFeed'=> '\d+']);

        Route::get('form-description-user/{idUser}', [
            'uses' => 'NetframeController@getFormDescriptionUser',
        ])->where(['idUser'=> '\d+']);

        Route::post('publish-description-user', [
            'uses' => 'NetframeController@postPublishDescriptionUser',
        ]);

        Route::get('form-training-user/{idUser}', [
            'uses' => 'NetframeController@getFormTrainingUser',
        ])->where(['idUser'=> '\d+']);

        Route::post('publish-training-user', [
            'uses' => 'NetframeController@postPublishTrainingUser',
        ]);

        Route::get('form-share-media/{mediaId}/{id?}', [
            'as' => 'form.share.media',
            'uses' => 'NetframeController@getFormShareMedia',
        ])->where(['mediaId'=> '\d+'])
          ->where(['id'=> '\d+']);

        Route::post('publish-share-media', [
            'uses' => 'NetframeController@postPublishShareMedia',
        ]);

        Route::get('form-comment-media/{mediaId}', [
            'as' => 'form.comment.media',
            'uses' => 'NetframeController@getFormCommentMedia',
        ])->where(['mediaId'=> '\d+']);
    });

        Route::get('pdf-viewer/view', [
            'as' => 'media.pdf.viewer',
            'uses' => 'NetframeController@getPDFViewer',
        ]);

    #
    #-----------------------------------------POSTING-----------------------------------------------
    #
    Route::group(['namespace' => 'User', 'prefix' => 'posting', 'middleware' => 'checkAuth'], function () {
        Route::match(['GET', 'POST'], '{post_type?}/{post_id?}', [
            'as' => 'posting.default',
            'uses' => 'PostingController@posting'
        ])
        ->where([
            'post_id'=> '\d+',
        ]);
    });

    #
    #-----------------------------------------EMOJIS-----------------------------------------------
    #
    Route::group(['prefix' => 'emojis', 'middleware' => 'checkAuth'], function () {
        Route::get('/', [
            'as' => 'emojis.list',
            'uses' => 'EmojisController@list'
        ]);

        Route::post('emojis', [
            'as' => 'emojis.emojis',
            'uses' => 'EmojisController@emojis'
        ]);
    });

    #
    #-----------------------------------------NOTIFICATIONS-----------------------------------------------
    #
    Route::group(['namespace' => 'User', 'prefix' => 'notifications', 'middleware' => 'checkAuth'], function () {
        Route::get('lasts', [
            'as' => 'notifications.lasts',
            'uses' => 'NotificationsController@lasts'
        ]);

        Route::match(['GET', 'POST'], 'all', [
            'as' => 'notifications.results',
            'uses' => 'NotificationsController@notifications'
        ]);

        Route::get('notif-join/', [
            'as' => 'notify.join',
            'uses' => 'NotificationsController@notifyJoin'
        ])->where([
            'profile_id' => '\d+',
            'type' => '[a-z]+',
            'user_from' => '\d+'
        ]);
    });

    #
    #-----------------------------------------FRIENDS-----------------------------------------------
    #

    Route::group(['namespace' => 'User', 'prefix' => 'friends', 'middleware' => 'checkAuth'], function () {
        Route::post('add-friend', [
            'as' => 'friend.ask',
            'uses' => 'FriendsController@addFriend'
        ]);

        Route::post('firend-answer/{action}', [
            'as' => 'friend.answer',
            'uses' => 'FriendsController@friendAnswer'
        ]);

        Route::get('friends', [
            'middleware' => 'checkAuth',
            'as' => 'friends.results',
            'uses' => 'FriendsController@friends'
        ]);

        Route::post('delete-friend', [
            'middleware' => 'checkAuth',
            'as' => 'delete.friends',
            'uses' => 'FriendsController@deleteFriend'
        ]);
    });

    #--------------------- ANNUAORE -------------------#

    Route::group(['namespace' => 'User', 'prefix' => 'directory', 'middleware' => 'checkAuth'], function () {
        // Route::post('add-friend', [
        //     'as' => 'friend.ask',
        //     'uses' => 'DirectoryController@addFriend'
        // ]);

        // Route::post('firend-answer/{action}', [
        //     'as' => 'friend.answer',
        //     'uses' => 'FriendsController@friendAnswer'
        // ]);

        Route::get('/', [
            'middleware' => 'checkAuth',
            'as' => 'directory.home',
            'uses' => 'DirectoryController@home'
        ]);

        Route::post('scroll', [
            'as' => 'directory.scroll',
            'uses' => 'DirectoryController@scroll'
        ]);

        Route::post('tooltip', [
            'as' => 'directory.tooltip',
            'uses' => 'DirectoryController@tooltip'
        ]);

        Route::post('delete-friend', [
            'middleware' => 'checkAuth',
            'as' => 'directory.delete',
            'uses' => 'DirectoryController@deleteFriend'
        ]);
    });



    Route::group(['namespace' => 'User', 'prefix' => 'office', 'middleware' => 'checkAuth'], function () {

        Route::get('/', [
            'as' => 'office.home',
            'uses' => 'OfficeController@home'
        ]);

        Route::match(['GET','POST'], '/create/{documentType}/{profileType}/{profileId}/{mediasFolder?}', [
            'as' => 'office.create',
            'uses' => 'OfficeController@create'
        ])->where([
            'documentType' => 'document|spreadsheet|presentation',
            'mediasFolder' => '\d+',
        ]);

        Route::get('/document/{documentId}', [
            'as' => 'office.document',
            'uses' => 'OfficeController@document'
        ])->where('documentId', '\d+');
    });

    # Route to onlyofficeds
    Route::get('/onlyofficeds', [
        'as' => 'onlyofficeds'
    ]);


    #--------------------- ANNUAIRE -------------------#

    Route::group(['namespace' => 'User', 'prefix' => 'tasks', 'middleware' => 'checkAuth'], function () {

        Route::get('/', [
            'as' => 'task.home',
            'uses' => 'TaskController@home'
        ]);

        Route::get('project/{project}', [
            'as' => 'task.project',
            'uses' => 'TaskController@project'
        ]);

        Route::match(['POST'], 'delete', [
            'as' => 'task.delete',
            'uses' => 'TaskController@delete'
        ]);

        Route::post('archive', [
            'as' => 'task.archive',
            'uses' => 'TaskController@archive'
        ]);

        Route::match(['GET', 'POST'], 'add-project', [
            'as' => 'task.addProject',
            'uses' => 'TaskController@addProject'
        ]);

        Route::match(['GET', 'POST'], 'edit-project/{projectId}', [
            'as' => 'task.editProject',
            'uses' => 'TaskController@addProject'
        ]);

        Route::match(['GET', 'POST'], 'details/{projectId}', [
            'as' => 'task.detailsProject',
            'uses' => 'TaskController@detailsProject'
        ]);

        Route::match(['GET', 'POST'], 'add-template/{projectId?}', [
            'as' => 'task.addTemplate',
            'uses' => 'TaskController@addTemplate'
        ]);

        Route::match(['GET', 'POST'], 'edit-template/{template}', [
            'as' => 'task.editTemplate',
            'uses' => 'TaskController@editTemplate'
        ]);

        Route::match(['GET'], 'edit-templates', [
            'as' => 'task.editTemplates',
            'uses' => 'TaskController@editTemplates'
        ]);

        Route::match(['POST'], 'delete-template/{template}', [
            'as' => 'task.deleteTemplate',
            'uses' => 'TaskController@deleteTemplate'
        ]);

        Route::match(['GET', 'POST'], 'get-cols', [
            'as' => 'task.getCols',
            'uses' => 'TaskController@getCols'
        ]);

        Route::match(['GET', 'POST'], 'add-task/{project}', [
            'as' => 'task.addTask',
            'uses' => 'TaskController@addTask'
        ]);

        Route::match(['POST'], 'add-task-col', [
            'as' => 'task.addTaskCol',
            'uses' => 'TaskController@addTaskCol'
        ]);

        Route::match(['GET', 'POST'], 'edit-task/{task}', [
            'as' => 'task.editTask',
            'uses' => 'TaskController@editTask'
        ]);

        Route::match(['GET', 'POST'], 'duplicate-task', [
            'as' => 'task.duplicate',
            'uses' => 'TaskController@duplicateTask'
        ]);

        Route::match(['GET', 'POST'], '{projectId}/link/{taskId}', [
            'as' => 'task.link',
            'uses' => 'TaskController@linkTask'
        ]);

        Route::match(['GET', 'POST'], '{projectId}/sub', [
            'as' => 'task.sub',
            'uses' => 'TaskController@sub'
        ]);

        Route::match(['GET', 'POST'], 'comments/{taskId}', [
            'as' => 'task.comment',
            'uses' => 'TaskController@comment'
        ]);

        Route::match(['GET','POST'], 'users', [
            'as' => 'task.users',
            'uses' => 'TaskController@users'
        ]);

        Route::get('validation', [
            'as' => 'task.validation',
            'uses' => 'TaskController@validation'
        ]);

        Route::post('revive', [
            'as' => 'task.revive',
            'uses' => 'TaskController@revive'
        ]);
    });

    #
    #----------------------------------------- COLLAB -----------------------------------------------
    #

    Route::get('collab/{path?}', [
        'as' => 'collab.home',
        'uses' => 'User\ColabController@home',
    ]);
    //Route::view('collab/{path?}', 'colab.app');

    Route::group(['namespace' => 'User', 'prefix' => 'colab', 'middleware' => 'checkAuth'], function () {

        Route::match(['POST'], '/user', [
            'as' => 'colab.user',
            'uses' => 'ColabController@getUsers'
        ]);
    });

    Route::group(['namespace' => 'User', 'prefix' => 'collab', 'middleware' => 'checkAuth'], function () {
        Route::match(['POST'], '/upload-file', [
            'as' => 'collab.upload',
            'uses' => 'ColabController@upload'
        ]);
    });

    #
    #-----------------------------------------INFINITE SCROLL-----------------------------------------------
    #

    Route::match(['GET', 'POST'], 'infinite-scroll/{profile_type}/{profile_id}/{last_time}', [
        'middleware' => 'checkAuth',
        'as' => 'infinite_feed',
        'uses' => 'User\PageController@infiniteNewsFeed'
    ])->where([
        'profile_id' => '\d+',
        'profile_type' => '\w+'
    ]);

    Route::match(['GET', 'POST'], 'infinite-timemine/{last_time}', [
        'middleware' => 'checkAuth',
        'as' => 'infinite_timeline',
        'uses' => 'User\ProfileController@infiniteTimeline'
    ]);





    #-------------------------------------------DRIVE-------------------------------------------------------

    Route::group(['domain' => 'drive-connect.'.env('APP_BASE_DOMAIN'), 'namespace' => 'User'], function () {
        Route::match(['GET', 'POST'], 'medias/import-folder/{profileType}/{profileId}/{idFolder?}', [
            'as' => 'xplorer_import_folder',
            'uses' => 'MediaController@importFolder'
        ])->where('profileType', 'user|house|community|project')
          ->where('profleId', '\d+')
          ->where('idFolder', '\d+');

        Route::match(['GET'], 'drive-authorize/{drive}', [
            'middleWare' => 'checkAuth',
            'as' => 'drive_authorize',
            'uses' => 'MediaController@driveAuthorize'
        ]);

        Route::get('calendar/import', [
            'as' => 'calendar.import',
            'uses' => 'CalendarController@import',
        ]);

        Route::get('calendar/authorize/{type}', [
            'as' => 'calendar.authorize',
            'uses' => 'CalendarController@calendarAuthorize',
        ]);
    });
});
