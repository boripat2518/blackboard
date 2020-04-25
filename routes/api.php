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

Route::group(['middleware' => 'guest:api'], function(){
  Route::post('/v1/login', 'Api\v1\UserController@login')->name('api.login');
  Route::post('/v1/register', 'Api\v1\UserController@register')->name('api.register');

  Route::get('/v1/home/category', 'Api\v1\CategoryController@all')->name('api.category.list');
  Route::get('/v1/lesson/{id}', 'Api\v1\LessonController@show')->name('api.lesson.show');
  Route::get('/v1/category/lesson/{id}', 'Api\v1\LessonController@searchByCategory')->name('api.category.lesson');
});

Route::group(['middleware' => 'auth:api'], function(){
//  Route::get('/user', 'Api\v1\UserController@getUser');
  Route::get('/v1/account/profile', 'Api\v1\UserController@user')->name('api.profile');

  Route::post('/v1/lesson/create', 'Api\v1\LessonController@create')->name('api.lesson.create');
  Route::post('/v1/lesson/{id}/update', 'Api\v1\LessonController@update')->name('api.lesson.update');

  Route::post('/v1/shop/create', 'Api\v1\RoomController@create')->name('api.room.create');
});
