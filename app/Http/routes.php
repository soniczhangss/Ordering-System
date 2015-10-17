<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/{home}', [
    'middleware' => 'auth',
    'uses' => 'PagesController@home'
])->where('home', '(?:home)?');

Route::get('/ajax/calendar/{type}/{date}/{location?}', 'PagesController@calendar');
Route::get('/ajax/calendar/nav', 'PagesController@nav');
Route::get('/ajax/order/rm/{action}', 'PagesController@order');
Route::get('/ajax/settings', 'PagesController@settings');
Route::post('/email/reset', 'PagesController@emailReset');
Route::get('/ajax/settings/userupdate', 'PagesController@userInfoUpdate');
Route::get('/ajax/accountmanagement', 'PagesController@accountManagement');
Route::post('/ajax/accountmanagement/userupdate', 'PagesController@userUpdate');
Route::get('/ajax/locationmanagement', 'PagesController@locationManagement');
Route::get('/ajax/locationmanagement/rm', 'PagesController@locationCreate');
Route::get('/ajax/locationmanagement/json', 'PagesController@locationManagementJson');
Route::post('/ajax/locationmanagement/locationupdate', 'PagesController@locationUpdate');
Route::get('/ajax/location/capacity/json', 'PagesController@locationCapacity');
Route::post('/ajax/tooltips/json', 'PagesController@tooltips');

Route::post('/ajax/location/store', 'LocationController@store');
Route::post('/ajax/location/update', 'LocationController@update');

Route::get('/ajax/companymanagement', 'PagesController@companyManagement');
Route::get('/ajax/companymanagement/json', 'PagesController@companyManagementJson');
Route::post('/ajax/companymanagement/companyupdate', 'PagesController@companyUpdate');
Route::get('/ajax/companymanagement/rm', 'PagesController@companyCreate');

Route::post('/ajax/company/store', 'CompanyController@store');
Route::post('/ajax/company/update', 'CompanyController@update');

Route::get('/ajax/accountmanagement/json', 'PagesController@accountManagementJson');
Route::get('/ajax/report', 'PagesController@report');
Route::get('/ajax/report/json', 'PagesController@reportJson');
Route::post('/ajax/orders/delete/json', 'PagesController@ordersDeleteJson');

Route::post('/ajax/order/d', 'OrdersController@destroy');
Route::resource('/ajax/order', 'OrdersController');

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('/ajax/whoami', 'PagesController@myRole');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');