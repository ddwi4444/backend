<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KomikController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register']);
Route::post('recover', [AuthController::class, 'recover'])->name('recover');
Route::get('verifyRegister/{verification_code}', [AuthController::class, 'verifyUser'])->name('verifyRegister');
Route::post('resetPassword/{uuid}', [AuthController::class, 'resetPassword'])->name('resetPassword');



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

// Route::group(['middleware' => ['jwt.auth']], function() {
//     Route::post('logout', [AuthController::class, 'logout']); 

//     Route::get('test', function(){
//         return response()->json(['foo'=>'bar']);
//     });
// });


// Komik
Route::post('create-komik', [KomikController::class, 'create'])->middleware('role:admin,student,osis');
Route::post('update-komik/{id}', [KomikController::class, 'update'])->middleware('role:admin,student,osis');
Route::delete('delete-komik/{id}', [KomikController::class, 'delete'])->middleware('role:admin,student,osis');
