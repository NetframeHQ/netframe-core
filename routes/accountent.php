<?php
/*
  |--------------------------------------------------------------------------
  | Application Routes Accountent
  |--------------------------------------------------------------------------
  |
 */

Route::group(['prefix' => 'accountent', 'namespace' => 'Accountent'], function () {

    Route::match(['get', 'post'], 'login', [
        'as' => 'accountent.login',
        'middleware' => 'accountentHome',
        'uses' => 'AuthController@login'
    ]);

    Route::get('logout', [
        'as' => 'accountent.logout',
        'uses' => 'AuthController@logout',
    ]);

    Route::match(['get', 'post'], 'remind-password/{token}', [
        'as' => 'accountent.remind-password',
        'middleware' => 'accountentHome',
        'uses' => 'AuthController@remindPassword'
    ]);

    Route::group(['prefix' => '/', 'middleware'=>'checkAccountent'], function () {

        /*============= ROUTES DASHBOARD =================== */

        Route::get('/', [
            'as' => 'accountent.home',
            'uses' => 'AccountentController@index'
        ]);

        /*Route::get('generate-bills/{action?}', [
            'as' => 'accountent.test',
            'uses' => 'AccountentController@generateBills'
        ]);*/

         Route::get('billing/{number?}', [
            'as' => 'accountent.billing',
            'uses' => 'AccountentController@billing'
         ]);

         Route::get('pdf/{number?}', [
            'as' => 'accountent.pdf',
            'uses' => 'AccountentController@pdf'
         ]);

        Route::match(['GET', 'POST'], 'paymentinfos/{type?}', [
            'as' => 'accountent.paymentinfos',
            'uses' => 'AccountentController@paymentinfos'
        ]);

        Route::match(['GET', 'POST'], 'infos', [
            'as' => 'accountent.infos',
            'uses' => 'AccountentController@infos'
        ]);

        Route::match(['GET', 'POST'], 'pay/{number?}', [
            'as' => 'accountent.pay',
            'uses' => 'AccountentController@pay'
        ]);
    });
});
