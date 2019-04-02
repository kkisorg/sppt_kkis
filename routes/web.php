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

// Auth middleware
// Reference: https://stackoverflow.com/a/29303878
Route::post('login', ['as' => 'login', 'uses' => 'UserController@login']);

// Public Controller
Route::get('/', 'PublicController@main_menu');

// User Controller
Route::get('/login', 'UserController@login');
Route::post('/authenticate', 'UserController@authenticate');
Route::get('/logout', 'UserController@logout');
Route::post('/register', 'UserController@insert');
Route::get('/activate_account/{token}', 'UserController@activate');
Route::get('/edit_profile', 'UserController@edit_profile');
Route::post('/update_profile', 'UserController@update_profile');
Route::get('/edit_password', 'UserController@edit_password');
Route::post('/update_password', 'UserController@update_password');
Route::get('/forget_password', 'UserController@forget_password');
Route::post('/insert_password_reset', 'UserController@insert_password_reset');
Route::get('/reset_password/{token}', 'UserController@reset_password');
Route::post('/update_forgotten_password', 'UserController@update_forgotten_password');
