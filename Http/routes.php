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
        'as' => 'content.index',
        'uses' => 'ContentController@index'
    ]);


    /**
     * Sections
     */

    Route::get('/content/sections/pagination', [
        'as' => 'sections.pagination',
        'uses' => 'SectionController@pagination'
    ]);


    /**
     * Channels
     */

    Route::get('/content/channels/{channel}/edit', [
        'as' => 'channels.edit',
        'uses' => 'ChannelController@edit'
    ]);

    Route::put('/content/channels/{channel}', [
        'as' => 'channels.update',
        'uses' => 'ChannelController@update'
    ]);

    
    /**
     * Entries
     */

    Route::get('/content/entries/pagination', [
        'as' => 'entries.pagination',
        'uses' => 'EntryController@pagination'
    ]);
    
    Route::get('/content/entries/{entry}/edit', [
        'as' => 'entries.edit',
        'uses' => 'EntryController@edit'
    ]);

    Route::put('/content/entries/{entry}', [
        'as' => 'entries.update',
        'uses' => 'EntryController@update'
    ]);

    Route::delete('/content/entries/{entry}', [
        'as' => 'entries.destroy',
        'uses' => 'EntryController@destroy'
    ]);

    Route::delete('/content/entries/attachment/{entry}/{language}', [
        'as' => 'entries.destroy_attachment',
        'uses' => 'EntryController@destroyAttachment'
    ]);

    Route::get('/content/entries/create/{channelId?}', [
        'as' => 'entries.create',
        'uses' => 'EntryController@create'
    ]);

    Route::post('/content/entries/{channelId?}', [
        'as' => 'entries.store',
        'uses' => 'EntryController@store'
    ]);

    Route::get('/content/entries/widgets', [
        'as' => 'entries.widgets.index',
        'uses' => 'EntryController@widgets'
    ]);
});

