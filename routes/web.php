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

// Media Controller
Route::get('/media', 'MediaController@index');
Route::get('/media/create', 'MediaController@create');
Route::post('/media/insert', 'MediaController@insert');
Route::get('/media/edit/{media_id}', 'MediaController@edit');
Route::post('/media/update', 'MediaController@update');
Route::get('/media/delete/{media_id}', 'MediaController@delete');

// API Controller
Route::post('/api/image_upload', 'APIController@image_upload');

// Announcement Request Controller
Route::get('/announcement_request', 'AnnouncementRequestController@index');
Route::get('/announcement_request/create', 'AnnouncementRequestController@create');
Route::post('/announcement_request/insert', 'AnnouncementRequestController@insert');
Route::get('/announcement_request/edit/{announcement_request_id}', 'AnnouncementRequestController@edit');
Route::post('/announcement_request/update', 'AnnouncementRequestController@update');
Route::get('/announcement_request/view/{announcement_request_id}', 'AnnouncementRequestController@view');
Route::get('/announcement_request/delete/{announcement_request_id}', 'AnnouncementRequestController@delete');

// Announcement Controller
Route::get('/announcement', 'AnnouncementController@index');
Route::get('/announcement/approve', 'AnnouncementController@approve');
Route::get('/announcement/create/{announcement_request_id}', 'AnnouncementController@create');
Route::post('/announcement/insert', 'AnnouncementController@insert');
Route::get('/announcement/edit/{announcement_request_id}', 'AnnouncementController@edit');
Route::post('/announcement/update', 'AnnouncementController@update');
Route::get('/announcement/view/{announcement_id}', 'AnnouncementController@view');
Route::get('/announcement/delete/{announcement_id}', 'AnnouncementController@delete');

// Offline Distribution Controller
Route::get('/offline_distribution', 'OfflineDistributionController@index');
Route::get('/offline_distribution/create', 'OfflineDistributionController@create');
Route::post('/offline_distribution/insert', 'OfflineDistributionController@insert');
Route::get('/offline_distribution/edit/{offline_distribution_id}', 'OfflineDistributionController@edit');
Route::post('/offline_distribution/update', 'OfflineDistributionController@update');
Route::get('/offline_distribution/view/{offline_distribution_id}', 'OfflineDistributionController@view');
Route::get('/offline_distribution/delete/{offline_distribution_id}', 'OfflineDistributionController@delete');
Route::get('/offline_distribution/edit_announcement/{offline_distribution_id}', 'OfflineDistributionController@edit_announcement');
Route::post('/offline_distribution/update_announcement', 'OfflineDistributionController@update_announcement');
