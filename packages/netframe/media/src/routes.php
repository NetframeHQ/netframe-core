<?php

Route::group(array('', 'prefix' => 'media', 'middleware' => ['web', 'checkAuth']), function () {
    Route::post('import', ['as' => 'media_import', 'uses' => 'Netframe\Media\MediaController@import']);
    Route::post('upload', ['as' => 'media_upload', 'uses' => 'Netframe\Media\MediaController@upload']);
    Route::match(
        array('GET', 'POST'),
        'edit/{id}',
        [
            'as' => 'media_edit',
            'uses' => 'Netframe\Media\MediaController@edit'
        ]
    );
    //Route::post('delete/{id}', ['as' => 'media_delete', 'uses' => 'Netframe\Media\MediaController@delete']);
    Route::get('list.json', ['as' => 'media_json_list', 'uses' => 'Netframe\Media\MediaController@jsonList']);

    //Route::get('attachment', ['as' => 'media_attachment', 'uses' => 'Netframe\Media\ModalController@attachment']);

    Route::get('download/{id}', [
        'as' => 'media_download',
        'uses' => 'Netframe\Media\MediaController@download'
    ]);
});
