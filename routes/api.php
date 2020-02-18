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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');


Route::post('/register', 'PassportController@register');
Route::post('/login', 'PassportController@login');
Route::post('/refresh', 'PassportController@refresh');
Route::post('/logout', 'PassportController@logout');

//
//Route::get('test', function () {
//    return 'ok';
//})->middleware('auth');

//
//Route::get('test', function () {
//    return 'ok';
//})->middleware('scopes:test2');


Route::get('test', function () {
    if(auth()->user()->tokenCan('test1')){
        return "123";
    }
})->middleware('auth');


Route::post('tt', function () {
    return 'ok';
});



Route::get('testcccc', function () {
    return auth()->user();
})->middleware('auth');


Route::resource('user', 'UserController')->middleware('auth');
Route::post('/user/search', 'UserController@search')->middleware('auth');
Route::post('/user/getVolunteerList', 'UserController@getVolunteerList')->middleware('auth');




Route::resource('paragraph', 'ParagraphController')->middleware('auth');
Route::post('/paragraph/search', 'ParagraphController@search')->middleware('auth');
Route::post('/paragraph/assignTansTask', 'ParagraphController@assignTansTask')->middleware('auth');
Route::post('/paragraph/cancelTanslator', 'ParagraphController@cancelTanslator')->middleware('auth');
Route::post('/paragraph/getMyTasks', 'ParagraphController@getMyTasks')->middleware('auth');

// 前台手册页面的路由
Route::post('/manual/getAnkiManual', 'front\MamualController@getAnkiManual');
Route::post('/manual/getAndroidManual', 'front\MamualController@getAndroidManual');
Route::post('/manual/getIosManual', 'front\MamualController@getIosManual');



Route::resource('cartoon', 'CartoonController');
Route::resource('article', 'ArticleController');
Route::resource('deck', 'DeckController');
Route::resource('category', 'CategoryController');
Route::resource('right', 'RightController');
Route::resource('role', 'RoleController');
