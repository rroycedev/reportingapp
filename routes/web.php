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
    return view('dashboard');
});
Auth::routes();

Route::get('/validate', 'ValidateController@index')->name('validate');
Route::post('/validatelogin', 'ValidateController@validatelogin')->name('validatelogin');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
	Route::get('table-list', function () {
		return view('pages.table_list');
	})->name('table');

	Route::get('typography', function () {
		return view('pages.typography');
	})->name('typography');

	Route::get('icons', function () {
		return view('pages.icons');
	})->name('icons');

	Route::get('map', function () {
		return view('pages.map');
	})->name('map');

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('rtl-support', function () {
		return view('pages.language');
	})->name('language');

	Route::get('upgrade', function () {
		return view('pages.upgrade');
	})->name('upgrade');
});

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::get('reportmaint/groupmgmt', ['uses' => 'GroupManagementController@index']);
        Route::get('reportmaint/reportmgmt', ['uses' => 'ReportManagementController@index']);
        Route::get('reportmaint/settings', ['uses' => 'SettingsController@index']);

        Route::get('bizops/employees', ['uses' => 'BizopsController@employees']);
        Route::get('bizops/territories', ['uses' => 'BizopsController@territories']);

        Route::get('cid/transactiontraceback', ['uses' => 'CIDController@transactiontraceback']);
        Route::get('cid/iptracking', ['uses' => 'CIDController@iptracking']);

        Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

//	Route::post('login', ['uses' => 'Auth\LoginController@dologin']);
});

