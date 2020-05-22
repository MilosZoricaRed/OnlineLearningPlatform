<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\TrainingController;

Route::get('usericon/{media}', 'UserController@showUserIcon');
Route::get('slideimage/{media}', 'SlideController@showSlideImage');
Route::get('slidevideo/{media}', 'SlideController@showSlideVideo');
Route::get('slideaudio/{media}', 'SlideController@showSlideAudio');
Route::get('trainingdetail/{media}', 'TrainingController@showTrainingImagesDetail');
Route::get('traininglist/{media}', 'TrainingController@showTrainingImagesList');
//Route::post('reset/password', 'RegisterController@passwordReset');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
