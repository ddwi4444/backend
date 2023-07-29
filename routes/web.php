<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Controller;

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
    return view('welcome');
});

Route::get('user/verify/{verification_code}', [AuthController::class, 'verifyUser']);

// Route::get('user/verify/{verification_code}', [AuthController::class, 'ResetPasswordController@showResetForm'])->name('password.request');
// Route::get('user/verify/{verification_code}', [AuthController::class, 'ResetPasswordController@postReset'])->name('password.request');
