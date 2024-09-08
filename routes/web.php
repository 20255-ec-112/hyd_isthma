<?php

use App\Helpers\SmsHelper;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Auth::routes(['register' => false]);
Route::middleware('auth')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::post('/', 'DashboardController@index')->name('dashboard'); // for dashboard charts
    // Members
    Route::resource('members', 'MembersController');
    // Registration
    Route::resource('registrations', 'RegistrationController');
    // Notifications
    Route::resource('notifications', 'NotificationsController');
    // Permissions
    Route::resource('permissions', 'PermissionsController');
    // Programs
    Route::resource('sessiontheme', 'SessionThemeController');
    // Speakers
    Route::resource('programSpeakers', 'ProgramSpeakerController');
    // Programs
    Route::resource('programs', 'ProgramsController');
    // Users
    Route::resource('user', 'UserController');
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('familyDetailsReport', 'ReportsController@familyDetailsReport')->name('family-details-report');
        Route::get('paymentDetailsReport', 'ReportsController@paymentDetailsReport')->name('payment-details-report');
        Route::get('arrivalReport', 'ReportsController@arrivalReport')->name('arrival-report');
        Route::get('departureReport', 'ReportsController@departureReport')->name('departure-report');
        Route::get('commonDataReport', 'ReportsController@commonDataReport')->name('common-data-report');
        Route::get('purchaseDataReport', 'ReportsController@purchaseDataReport')->name('purchase-data-report');
        Route::get('sightSeeingDetailsReport', 'ReportsController@sightSeeingDetailsReport')->name('sight-seeing-details-report');
        Route::get('globalReport', 'ReportsController@globalReport')->name('global-report');
    });
    // filter helpers
    Route::post('getDivisions', 'DashboardController@getDivisions')->name('getDivisions');
    Route::post('getUnits', 'DashboardController@getUnits')->name('getUnits');
    Route::get('getStationNames', 'DashboardController@getStationNames')->name('get-station-names');
});
// Route::get('syncrukundata', 'ReportsController@syncRukunData')->name('sync-rukun-data');
// temp routes
Route::prefix('delete')->group(function () {
    Route::get('account', 'DeleteAccountController@index')->name('delete-account');
    Route::post('account', 'DeleteAccountController@delete')->name('delete-account');
    Route::get('login', 'DeleteAccountController@login')->name('tmp-login');
    Route::post('otpVerify', 'DeleteAccountController@otpVerify')->name('tmp-otpVerify');
    Route::post('loginWithOtp', 'DeleteAccountController@loginWithOtp')->name('tmp-loginWithOtp');
    Route::get('logout', 'DeleteAccountController@logout')->name('tmp-logout');
});
