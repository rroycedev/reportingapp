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

Route::middleware('client_credentials:api')->group(function () {
//	Route::resource('tables', 'API\TableController');
	Route::get('reportfiles', 'API\ReportFilesController@find');
	Route::get('updatereportfilestatus', 'API\ReportFilesController@updateStatus');
	Route::get('updatereportstatus', 'API\ReportController@updateStatus');
});
