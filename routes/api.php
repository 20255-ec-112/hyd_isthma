<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/* version 1 */
Route::group(['prefix' => 'v1'], function () {
    /* auth */
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
        Route::post('verifyOtp', 'AuthController@verifyOtp');
        Route::post('verifyToken', 'AuthController@verifyToken');
    });
    /* protected routes */
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('getUserDetails', 'RegistrationController@index');
            Route::get('getUserDetailsTest', 'RegistrationController@getUserDetailsTest');
            Route::post('register', 'RegistrationController@register');
            Route::post('familyDetails', 'RegistrationController@updateFamilyDetails');
            Route::post('financialDetails', 'RegistrationController@updateFinancialDetails');
            Route::post('additionalDetails', 'RegistrationController@updateAdditionalDetails');
        });
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('listNotifications', 'NotificationsController@listNotifications');
            Route::get('getNotification/{id}', 'NotificationsController@getNotification');
        });
        Route::group(['prefix' => 'programs'], function () {
            Route::get('listPrograms', 'ProgramsController@listPrograms');
            Route::get('getProgram/{id}', 'ProgramsController@getProgram');
        });
        Route::post('logout', 'AuthController@logout');
        Route::post('deleteAccount', 'AuthController@deleteAccount');
    });
    Route::get('fetchUsers', 'DataFetchController@index');
    Route::get('getZones', 'AdminDataFetchController@getZoneNames');
});