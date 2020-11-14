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
Route::post('/login', $url . '\UserController@login')->name('login');
Route::post('/register', $url . '\UserController@register');

// ^ All Role
Route::group(['middleware' => 'auth:api'], function() {
    Route::get('/user/detail', 'App\Http\Controllers\UserController@detailUser');
    Route::get('/logout', 'App\Http\Controllers\UserController@logout');
});

//^ Guru Role
Route::group(['middleware' => ['auth:api', 'role:guru']], function() {
    Route::get('/laporan', 'App\Http\Controllers\GuruController@laporan')->middleware(['role:guru']);
});

//^ Murid Role
