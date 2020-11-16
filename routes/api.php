<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
$url = "App\Http\Controllers";
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

Route::get('/kembali', function() {
    return "not permitted";
})->name('kembali');
Route::get('email/verify/{id}', 'App\Http\Controllers\VerificationApiController@verify')->name('verificationapi.verify');
Route::post('email/resend', 'App\Http\Controllers\VerificationApiController@resend')->name('verificationapi.resend');

Route::post('/login', $url . '\UserController@login')->middleware('cekverified')->name('login');
Route::post('/register', $url . '\UserController@register')->name('register');

Route::get('/users/{role}', $url . '\UserController@getUsersByRole');

// ^ All Role
Route::group(['middleware' => 'auth:api', 'verified'], function() {
    Route::get('/user/detail', 'App\Http\Controllers\UserController@detailUser');
    Route::get('/logout', 'App\Http\Controllers\UserController@logout');
});

//^ Teacher's Role
Route::group(['middleware' => ['auth:api', 'role:teacher', 'cekverified']], function() {
    Route::get('/laporan', 'App\Http\Controllers\TeacherController@laporan');
});

//^ Student's Role
Route::group(['middleware' => ['auth:api', 'role:student']], function() {
    Route::post('/request-borrow', 'App\Http\Controllers\StudentController@requestBorrowing');
    Route::get('/history-borrow', 'App\Http\Controllers\StudentController@historyBorrowing');
    Route::get('/get-date', 'App\Http\Controllers\StudentController@getDate');
});
