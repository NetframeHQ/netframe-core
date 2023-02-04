<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});

Route::get('/officeds/download/{mediaId}', [
            'as' => 'office.download',
            'uses' => 'User\OfficeController@download'
        ])->where('mediaId', '\d+');

Route::post('/officeds/save/{mediaId}', [
            'as' => 'office.save',
            'uses' => 'User\OfficeController@save'
        ])->where('mediaId', '\d+');

Route::group(['namespace' => 'User', 'prefix' => '/'], function () {
    Route::get('/{slug}/docs/{docId?}', [
        'as' => 'colab.docs',
        'uses' => 'ColabController@docs'
    ]);
    Route::match(['GET','POST'], '/{slug}/add', [
        'as' => 'colab.add',
        'uses' => 'ColabController@add'
    ]);
    Route::match(['GET','POST'], '/{slug}/edit/{id}', [
        'as' => 'colab.edit',
        'uses' => 'ColabController@add'
    ]);
    Route::match(['POST'], '/{slug}/delete', [
        'as' => 'colab.delete',
        'uses' => 'ColabController@delete'
    ]);
    Route::match(['GET','POST'], '/{slug}/push', [
        'as' => 'colab.push',
        'uses' => 'ColabController@push'
    ]);
    Route::get('/{slug}/document/{documentId}', [
        'as' => 'colab.document',
        'uses' => 'ColabController@document'
    ]);
    Route::match(['GET','POST'], '/{slug}/document/{documentId}/steps', [
        'as' => 'colab.steps',
        'uses' => 'ColabController@steps'
    ]);
    Route::match(['GET','POST'], '/{slug}/document/{documentId}/telepointer', [
        'as' => 'colab.telepointer',
        'uses' => 'ColabController@telepointer'
    ]);
    Route::post('/subscribe', [
        'as' => 'colab.subscribe',
        'uses' => 'ColabController@subscribe'
    ]);
    Route::get('/{slug}/docs', [
        'as' => 'colab.docs',
        'uses' => 'ColabController@docs'
    ]);
    Route::get('/users/{userId}', [
        'as' => 'colab.users',
        'uses' => 'ColabController@users'
    ]);
});
