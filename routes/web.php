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
Route::get('/announcement/create/{announcement_request_id}', 'AnnouncementController@create');
Route::post('/announcement/insert', 'AnnouncementController@insert');
Route::get('/announcement/edit/{announcement_request_id}', 'AnnouncementController@edit');
Route::post('/announcement/update', 'AnnouncementController@update');
Route::get('/announcement/edit_distribution_schedule/{announcement_id}', 'AnnouncementController@edit_distribution_schedule');
Route::post('/announcement/update_distribution_schedule', 'AnnouncementController@update_distribution_schedule');
Route::get('/announcement/view/{announcement_id}', 'AnnouncementController@view');
Route::get('/announcement/delete/{announcement_id}', 'AnnouncementController@delete');
Route::get('/view_announcement', 'AnnouncementController@view_all');

// Offline Distribution Controller
Route::get('/offline_distribution', 'OfflineDistributionController@index');
Route::get('/offline_distribution/create', 'OfflineDistributionController@create');
Route::post('/offline_distribution/insert', 'OfflineDistributionController@insert');
Route::get('/offline_distribution/edit/{offline_distribution_id}', 'OfflineDistributionController@edit');
Route::post('/offline_distribution/update', 'OfflineDistributionController@update');
Route::get('/offline_distribution/view/{offline_distribution_id}', 'OfflineDistributionController@view');
Route::get('/offline_distribution/delete/{offline_distribution_id}', 'OfflineDistributionController@delete');
Route::get('/offline_distribution/edit_content/{offline_distribution_id}', 'OfflineDistributionController@edit_content');
Route::post('/offline_distribution/update_content', 'OfflineDistributionController@update_content');
Route::get('/view_offline_distribution', 'OfflineDistributionController@view_all');
Route::get('/offline_distribution/share/{offline_distribution_id}', 'OfflineDistributionController@share');

// Monthly Offline Distribution Schedule Controller
Route::get('/monthly_offline_distribution_schedule', 'MonthlyOfflineDistributionScheduleController@index');
Route::get('/monthly_offline_distribution_schedule/create', 'MonthlyOfflineDistributionScheduleController@create');
Route::post('/monthly_offline_distribution_schedule/insert', 'MonthlyOfflineDistributionScheduleController@insert');
Route::get('/monthly_offline_distribution_schedule/edit/{announcement_request_id}', 'MonthlyOfflineDistributionScheduleController@edit');
Route::post('/monthly_offline_distribution_schedule/update', 'MonthlyOfflineDistributionScheduleController@update');
Route::get('/monthly_offline_distribution_schedule/view/{announcement_request_id}', 'MonthlyOfflineDistributionScheduleController@view');
Route::get('/monthly_offline_distribution_schedule/delete/{announcement_request_id}', 'MonthlyOfflineDistributionScheduleController@delete');
Route::post('/monthly_offline_distribution_schedule/manual_invoke', 'MonthlyOfflineDistributionScheduleController@manual_invoke');

// Email Controller
Route::get('/email_send_schedule', 'EmailController@index');
Route::get('/email_send_schedule/view/{email_send_schedule_id}', 'EmailController@view');
Route::get('/email_send_schedule/manual_invoke/{email_send_schedule_id}', 'EmailController@manual_invoke');

// Announcement Online Media Publish Schedule Controller
Route::get('/announcement_online_media_publish_schedule', 'AnnouncementOnlineMediaPublishScheduleController@index');
Route::get('/announcement_online_media_publish_schedule/view/{schedule_id}', 'AnnouncementOnlineMediaPublishScheduleController@view');
Route::get('/announcement_online_media_publish_schedule/manual_invoke/{schedule_id}', 'AnnouncementOnlineMediaPublishScheduleController@manual_invoke');

// Account management
Route::get('/account_management', 'UserController@index');
Route::get('/account_management/resend_activation_email/{user_id}', 'UserController@resend_activation_email');
Route::get('/account_management/force_activate/{user_id}', 'UserController@force_activate');
Route::get('/account_management/update_admin_role/{user_id}', 'UserController@update_admin_role');
Route::get('/account_management/update_block_status/{user_id}', 'UserController@update_block_status');
