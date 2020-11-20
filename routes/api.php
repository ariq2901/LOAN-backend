<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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

//^ Guest
Route::get('/kembali', function() {return "not permitted";})->name('kembali');
Route::get('email/verify/{id}', 'App\Http\Controllers\VerificationApiController@verify')->name('verificationapi.verify');
Route::post('email/resend', 'App\Http\Controllers\VerificationApiController@resend')->name('verificationapi.resend');
Route::post('/login', $url . '\UserController@login')->middleware('cekverified')->name('login');
Route::post('/register', $url . '\UserController@register')->name('register');
Route::get('/users/{role}', $url . '\UserController@getUsersByRole');

// ^ All Role
Route::group(['middleware' => 'auth:api', 'cekverified'], function() {
    Route::get('/user/detail', 'App\Http\Controllers\UserController@detailUser');
    Route::get('/logout', 'App\Http\Controllers\UserController@logout');
});

//^ Teacher's Role
Route::group(['middleware' => ['auth:api', 'role:teacher|musyrif']], function() {
    Route::get('/laporan', 'App\Http\Controllers\TeacherController@laporan');
    Route::get('/list-approval/{approver}', 'App\Http\Controllers\TeacherController@listApproval');
    Route::get('/show-approval/{id}', 'App\Http\Controllers\TeacherController@showApproval');
    Route::post('/approvement/{id}', 'App\Http\Controllers\TeacherController@approvement');
    Route::get('/show-assignment/{id}', 'App\Http\Controllers\TeacherController@showAssignment');
});

//^ Student's Role
Route::group(['middleware' => ['auth:api', 'role:student']], function() {
    Route::post('/request-borrow', 'App\Http\Controllers\StudentController@requestBorrowing');
    Route::get('/history-borrow/{per_page?}', 'App\Http\Controllers\StudentController@historyBorrowing');
    Route::get('/get-date', 'App\Http\Controllers\StudentController@getDate');
    Route::post('/assignment/{borrowingId}', 'App\Http\Controllers\StudentController@setorTugas');
    Route::post('/upload-img', 'App\Http\Controllers\StudentController@uploadImage');
});

// Route::get('/peminjamanemail', function() {
    //     return view('peminjaman');
    // });

Route::get('file/image/{id}', 'App\Http\Controllers\UserController@imageDownload');

//^ Forgot Pass
Route::post('/forgot-password', function(Request $request) {
    $request->validate(["email" => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT ? response()->json(["message" => __($status)], 200) : response()->json(["error" => __($status)], 400);
})->name('password.email');
Route::get('/reset-password/{token}', function ($token) {
    $email = $_GET['email'];
    return view('auth.reset-password', ['token' => $token, 'email' => $email]);
})->name('password.reset');
Route::post('/reset-password', function(Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) use ($request) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();

            $user->setRememberToken(Str::random(60));

            event(new PasswordReset($user));
        }
    );

    return $status == Password::PASSWORD_RESET ? view("inforeset", ['status' => __($status)]) : view("inforeset", ['status' => __($status)]);
})->name('password.update');