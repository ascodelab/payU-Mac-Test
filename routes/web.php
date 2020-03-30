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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'PayUController@index')->name('home');
Route::post('/create-code', 'PayUController@createCode')->name('createCode');
Route::get('/search-code', 'PayUController@searchCode')->name('searchCode');
Route::post('/edit-code', 'PayUController@editCode')->name('editCode');
Route::get('/delete-code/{id}', 'PayUController@deleteCode')->name('deleteCode');