<?php

Route::group(['prefix' => 'workflow', 'namespace' => 'Workflow'], function () {
    Route::get('list-actions/{objectType}', [
        'as' => 'wf.list.actions',
        'uses' => 'WorkflowController@listActions',
    ]);

    Route::post('choose-action', [
        'as' => 'wf.choose.actions',
        'uses' => 'WorkflowController@chooseAction',
    ]);

    Route::post('search-users', [
        'as' => 'wf.search.users',
        'uses' => 'WorkflowController@searchUsers',
    ]);

    Route::post('answer-action', [
        'as' => 'wf.answer.action',
        'uses' => 'WorkflowController@manageAnswer',
    ]);

    Route::get('delete/{id}', [
        'as' => 'wf.delete',
        'uses' => 'WorkflowController@delete',
    ]);
});
