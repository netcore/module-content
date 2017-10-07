<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'content::',
    'middleware' => ['web', 'auth.admin'],
    'namespace'  => 'Modules\Content\Http\Controllers\Admin'
], function () {

    Route::get('/content', [
        'as' => 'content.index',
        'uses' => 'ContentController@index'
    ]);

    Route::get('/content/sections/pagination', [
        'as' => 'sections.pagination',
        'uses' => 'SectionController@pagination'
    ]);

    Route::get('/content/channels/{channel}/edit', [
        'as' => 'channels.edit',
        'uses' => 'ChannelsController@edit'
    ]);
});
