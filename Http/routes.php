<?php

Route::group(['middleware' => 'web', 'prefix' => 'content', 'namespace' => 'Modules\Content\Http\Controllers'], function()
{
    Route::get('/', 'ContentController@index');
});
