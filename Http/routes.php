<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'content::',
    'middleware' => ['web', 'auth.admin'],
    'namespace'  => 'Modules\Content\Http\Controllers\Admin'
], function () {

    /**
     * Index
     */

    Route::get('/content', [
        'as'   => 'content.index',
        'uses' => 'ContentController@index'
    ]);


    /**
     * Sections
     */

    Route::get('/content/sections/pagination', [
        'as'   => 'sections.pagination',
        'uses' => 'SectionController@pagination'
    ]);


    /**
     * Channels
     */

    Route::get('/content/channels/{channel}/edit', [
        'as'   => 'channels.edit',
        'uses' => 'ChannelController@edit'
    ]);

    Route::put('/content/channels/{channel}', [
        'as'   => 'channels.update',
        'uses' => 'ChannelController@update'
    ]);


    /**
     * Entries
     */

    Route::get('/content/entries/pagination', [
        'as'   => 'entries.pagination',
        'uses' => 'EntryController@pagination'
    ]);

    Route::get('/content/entries/{entry}/edit', [
        'as'   => 'entries.edit',
        'uses' => 'EntryController@edit'
    ]);

    Route::get('/content/entries/{entry}/preview', [
        'as'   => 'entries.preview',
        'uses' => 'EntryController@preview'
    ]);

    Route::put('/content/entries/{entry}', [
        'as'   => 'entries.update',
        'uses' => 'EntryController@update'
    ]);

    Route::delete('/content/entries/{entry}', [
        'as'   => 'entries.destroy',
        'uses' => 'EntryController@destroy'
    ]);

    Route::delete('/content/entries/attachment/{attachment}', [
        'as'   => 'entries.destroy_attachment',
        'uses' => 'EntryController@destroyAttachment'
    ]);

    Route::get('/content/entries/create/{channelId?}', [
        'as'   => 'entries.create',
        'uses' => 'EntryController@create'
    ]);

    Route::post('/content/entries/{channelId?}', [
        'as'   => 'entries.store',
        'uses' => 'EntryController@store'
    ]);

    Route::post('/content/attachment/state', [
        'as'   => 'entries.attachment.state',
        'uses' => 'EntryController@attachmentState'
    ]);

    Route::get('/content/entries/widgets', [
        'as'   => 'entries.widgets.index',
        'uses' => 'EntryController@widgets'
    ]);

    Route::get('/content/entries/{entry}/revisions', [
        'as'   => 'entries.revisions',
        'uses' => 'EntryController@revisions'
    ]);

    Route::post('/content/entries/{entry}/create-draft', [
        'as'   => 'entries.create_draft',
        'uses' => 'EntryController@createDraft'
    ]);

    Route::post('/content/entries/{entry}/restore-revision', [
        'as'   => 'entries.restore_revision',
        'uses' => 'EntryController@restoreRevision'
    ]);
});

Route::group([
    'middleware' => ['web'],
    'namespace'  => 'Modules\Content\Http\Controllers\Api',
    'prefix'     => 'api',
    'as'         => 'api.'
], function () {
    Route::get('/content/get-pages/{locale?}', [
        'as'   => 'content.get-pages',
        'uses' => 'ContentController@getPages'
    ]);

    Route::get('/content/get-page/{key}/{locale?}', [
        'as'   => 'content.get-page',
        'uses' => 'ContentController@getPage'
    ]);
});
