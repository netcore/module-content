<?php

/*
Route::group(['middleware' => 'web', 'prefix' => 'content', 'namespace' => 'Modules\Content\Http\Controllers'], function()
{
    Route::get('/', 'ContentController@index');
});
*/

/*
//admin routes
Route::group([
    'prefix'     => 'admin',
    'as'         => 'user::',
    'middleware' => ['web', 'auth.admin'],
    'namespace'  => 'Modules\User\Http\Controllers\Admin'
], function () {
    Route::resource('users', 'UsersController');
});
*/

Route::group([
    'prefix'     => 'admin',
    'as'         => 'content::',
    'middleware' => ['web', 'auth.admin'],
    'namespace'  => 'Modules\Content\Http\Controllers\Admin'
], function () {
    Route::resource('pages', 'PageController');
});
