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
});

Route::group(['middleware' => 'auth:api'], function(){
  Route::get('/user', 'Api\v1\UserController@getUser');
  Route::get('/v1/lesson/{id}', 'Api\v1\LessonController@show')->name('api.lesson.show');
  Route::post('/v1/lesson/create', 'Api\v1\LessonController@create')->name('api.lesson.create');
  Route::patch('/v1/lesson/{id}', 'Api\v1\LessonController@update')->name('api.lesson.update');

  Route::get('/v1/home/category', 'Api\v1\CategoryController@all')->name('api.category.list');

  Route::resource('/v1/category', 'Api\v1\CategoryController');

});
