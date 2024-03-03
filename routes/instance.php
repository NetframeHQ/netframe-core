<?php

#
#-----------------------------------------INSTANCES-----------------------------------------------
#
Route::group(['prefix' => 'instance', 'namespace' => 'Instance', 'middleware' => 'instanceManager'], function () {
    Route::group(['prefix' => 'virtual-users', ], function () {
        Route::get('list/{userId}', [
            'as' => 'instance.virtualuser.list',
            'uses' => 'VirtualUserController@virtualUsers'
        ]);
        Route::match(['GET', 'POST'], 'edit/{userId}/{virtualUserId?}', [
            'as' => 'instance.virtualuser.edit',
            'uses' => 'VirtualUserController@editVirtualUser'
        ]);
        Route::get('delete/{virtualUserId}', [
            'as' => 'instance.virtualuser.delete',
            'uses' => 'VirtualUserController@deleteVirtualUser'
        ]);
        Route::post('disable', [
            'as' => 'instance.virtualuser.disable',
            'uses' => 'VirtualUserController@disableVirtualUser'
        ]);
    });

    Route::get('parameters/{parameter?}', [
        'as' => 'instance.parameters',
        'uses' => 'InstanceController@parameters'
    ]);

    Route::match(['GET', 'POST'], 'subscription/{action?}', [
        'as' => 'instance.subscription',
        'uses' => 'InstanceController@subscription'
    ]);

    Route::match(['GET', 'POST'], 'boarding/{action?}', [
        'as' => 'instance.boarding',
        'uses' => 'InstanceController@boarding'
    ]);

    Route::match(['GET', 'POST'], 'rights/{action?}', [
        'as' => 'instance.rights',
        'uses' => 'InstanceController@rights'
    ]);

    Route::match(['GET', 'POST'], 'auto-subscribe/{profileType?}', [
        'as' => 'instance.auto.subscribe',
        'uses' => 'InstanceController@autoSubscribe'
    ]);

    Route::match(['GET', 'POST'], 'profiles/{profileType}', [
        'as' => 'instance.profiles',
        'uses' => 'InstanceController@profiles'
    ])->where('profileType', 'users|projects|houses|communities');

    Route::match(['GET', 'POST'], 'edit/{id}', [
        'as' => 'instance.edit',
        'uses' => 'InstanceController@edit'
    ]);

    Route::match(['GET', 'POST'], 'manage-rights/{id}', [
        'as' => 'instance.manageRights',
        'uses' => 'InstanceController@manageRights'
    ]);

    Route::match(['GET', 'POST'], 'manage/{profileType}/{id}', [
        'as' => 'instance.manage',
        'uses' => 'InstanceController@manage'
    ]);

    Route::match(['GET', 'POST'], 'create', [
        'as' => 'instance.create',
        'uses' => 'InstanceController@create'
    ]);

    Route::match(['GET', 'POST'], 'groups', [
        'as' => 'instance.groups',
        'uses' => 'InstanceController@groups'
    ]);

    Route::match(['POST'], 'user', [
        'as' => 'instance.user',
        'uses' => 'InstanceController@getUsers'
    ]);

    Route::match(['GET', 'POST'], 'visitors', [
        'as' => 'instance.visitors',
        'uses' => 'InstanceController@visitors'
    ]);

    Route::post('profiles/activation/{profileType}', [
        'as' => 'instance.profile.activation',
        'uses' => 'InstanceController@activation',
    ])->where('profileType', 'projects|houses|communities');

    Route::match(['GET', 'POST'], 'invite', [
        'as' => 'instance.invite',
        'uses' => 'InstanceController@invite',
    ]);

    Route::match(['GET', 'POST'], 'apps', [
        'as' => 'instance.apps',
        'uses' => 'InstanceController@apps',
    ]);

    Route::match(['GET', 'POST'], 'usersdata', [
        'as' => 'instance.usersdata',
        'uses' => 'InstanceController@usersdata',
    ]);

    Route::post('delete-custom', [
        'as' => 'instances.delete-custom',
        'uses' => 'InstanceController@deleteCustom'
    ]);

    /*
     *
     * Routes form graphical custom
     *
     */
    Route::match(['GET', 'POST'], 'graphical/{customType?}', [
        'as' => 'instance.graphical',
        'uses' => 'GraphicalController@graphical',
    ])->where('customType', 'colors|colorsDark|logos|backgrounds|theme|buttons');

    Route::get('graphical/select-theme/{slug}', [
        'as' => 'instance.graphical.theme',
        'uses' => 'GraphicalController@selectTheme',
    ]);

    Route::post('upload', [
        'as' => 'instances.upload',
        'uses' => 'GraphicalController@upload'
    ]);

    Route::post('remove-media', [
        'as' => 'instances.remove.media',
        'uses' => 'GraphicalController@removeMedia'
    ]);

    /*
     *
     * Routes form instance stats
     *
     */
    Route::get('stats/{period?}', [
        'as' => 'instance.stats',
        'uses' => 'StatsController@home',
    ]);
});
