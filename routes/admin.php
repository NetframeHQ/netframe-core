<?php
/*
  |--------------------------------------------------------------------------
  | Application Routes Admin
  |--------------------------------------------------------------------------
  |
 */

Route::group(['prefix' => 'management', 'namespace' => 'Admin'], function () {

    Route::match(['get', 'post'], 'login', [
        'as' => 'management.login',
        'middleware' => 'adminHome',
        'uses' => 'AuthController@login'
    ]);

    Route::get('logout', [
        'as' => 'admin.logout',
        'uses' => 'AuthController@logout',
    ]);

    Route::group(['prefix' => '/', 'middleware'=>'checkAdmin'], function () {

        /*============= ROUTES ADMIN ADMIN =================== */
        Route::get('log-as/{id}', [
            'as' => 'admin.log.as',
            'uses' => 'InstanceController@logAs'
        ]);

        /*============= ROUTES DASHBOARD =================== */

        Route::get('/', [
            'as' => 'admin.home',
            'uses' => 'DashboardController@index'
        ]);

        /*============= ROUTES ADMINISTRATORS =================== */

        Route::group(['prefix' => 'admins'], function () {

            Route::get('/', array(
                'as' => 'admin.admins',
                'uses' => 'AdminsController@listing',
            ));

            Route::match(['GET', 'POST'], 'edit/{id?}', array(
                'as' => 'admin.edit',
                'uses' => 'AdminsController@edit',
            ));

            Route::get('delete/{id}', array(
                'as' => 'admin.delete',
                'uses' => 'AdminsController@delete',
            ));
        });

        /*============= ROUTES INSTANCES =================== */
        Route::group(['prefix' => 'instances'], function () {
            Route::match(['get', 'post'], '/', array(
                'as' => 'admin.instances.home',
                'uses' => 'InstanceController@home'
            ));

            Route::post('search', array(
                'as' => 'admin.instances.search',
                'uses' => 'InstanceController@searchInstance'
            ));

            Route::get('details/{id?}/{type?}', array(
                'as' => 'admin.instances.details',
                'uses' => 'InstanceController@detailInstance'
            ))->where('id', '\d+');

            Route::post('apps', array(
                'as' => 'admin.instances.apps',
                'uses' => 'InstanceController@updateApps'
            ));

            Route::match(['get', 'post'], 'user-password/{id?}', array(
                'as' => 'admin.instances.userpass',
                'uses' => 'InstanceController@userPassword'
            ))->where('id', '\d+');
        });

        /*============= ROUTES TRANSLATE =================== */

        Route::group(['prefix' => 'translation'], function () {

            Route::match(['get', 'post'], '/', array(
                'as' => 'admin.translate.home',
                'uses' => 'TranslationController@home'
            ));

            Route::match(['get', 'post'], 'form/category/{id?}', array(
                'as' => 'admin.translate.form.category',
                'uses' => 'TranslationController@formCategoryAction'
            ))->where('id', '\d+');

            Route::match(['get', 'post'], 'form/category-affect/{id?}', array(
                'as' => 'admin.translate.form.category.reaffect',
                'uses' => 'TranslationController@formCategoryAffect'
            ))->where('id', '\d+');



            Route::get('general/{lang?}', array(
                'as' => 'admin.translate.general',
                'uses' => 'TranslationController@general'
            ))->where('lang', '\w+');
        });

        /*============= ROUTES FOR STATISTICS =================== */

        Route::resource('statistics', 'StatisticController');

        Route::get('report/{reportName}', [
            'as' => 'admin.report',
            'uses' => 'StatisticController@report'
        ])->where('reportName', '\w+');
    });
});
