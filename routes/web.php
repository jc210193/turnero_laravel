<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'PublicDisplayController@index');
Route::post('/verify', 'OfficeController@verifyOffice');

Route::prefix('public')->group(function () {
    Route::get('shift', 'PublicDisplayController@shiftSelector')->name('select.speciality');
    Route::post('shift/get-speciality', 'SpecialityController@getSpeciality');
    Route::post('shift/get-channel', 'PublicDisplayController@getDataOffice');
    
    Route::post('verify-client', 'ClientController@verifyClient');
    Route::post('new-ticket', 'ShiftController@create');


    Route::get('display', 'PublicDisplayController@numberDisplay');
    Route::get('list-shift', 'PublicDisplayController@getListShifts');
    Route::post('get-shift', 'PublicDisplayController@getShift');
});


Route::get('dashboard/login', ['as' => 'login', 'uses' => 'Dashboard\LoginController@index']);
Route::post('dashboard/login', ['as' => 'login-dashboard', 'uses' => 'Dashboard\LoginController@store']);
Route::get('dashboard/logout', ['as' => 'logout', 'uses' => 'Dashboard\LoginController@logout']);

Route::group(['prefix' => 'dashboard', 'middleware'=> 'auth'], function() {
    Route::get('/', 'Dashboard\IndexController@index');

    Route::get('shift', 'DashboardController@adminShift');
    Route::post('get-user', 'DashboardController@getUser');
    Route::post('get-shift-advisor', 'DashboardController@getShiftAdvisor');
});




Route::get('prueba/{specialityId}', 'AdvisorController@selectAdvisor');
