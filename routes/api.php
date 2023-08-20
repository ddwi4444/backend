<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\KomenController;
use App\Http\Controllers\Api\KomikController;
use App\Http\Controllers\Api\MerchandiseController;
use App\Http\Controllers\Api\NPCController;
use App\Http\Controllers\Api\PortofolioController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubKomikController;
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

// User
Route::post('update-user/{uuid}', [StudentController::class, 'update'])->middleware('StudentOsisAdmin');

// Komik
Route::post('create-komik', [KomikController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-komik/{id}', [KomikController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-komik/{id}', [KomikController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-komik/{id}', [KomikController::class, 'read']);

// SubKomik
Route::post('create-subkomik/{id}', [SubKomikController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-subkomik/{id}', [SubKomikController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-subkomik/{id}', [SubKomikController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-subkomik/{id}', [SubKomikController::class, 'read']);


// NPC
Route::post('create-npc', [NPCController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-npc/{id}', [NPCController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-npc/{id}', [NPCController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-npc/{id}', [NPCController::class, 'read']);

// Merchandise
Route::post('create-merchandise', [MerchandiseController::class, 'create'])->middleware('role:admin');
Route::post('update-merchandise/{id}', [MerchandiseController::class, 'update'])->middleware('role:admin');
Route::delete('delete-merchandise/{id}', [MerchandiseController::class, 'delete'])->middleware('role:admin');
Route::post('read-merchandise/{id}', [MerchandiseController::class, 'read']);

// Portofolio
Route::post('create-portofolio', [PortofolioController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-portofolio/{id}', [PortofolioController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-portofolio/{id}', [PortofolioController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-portofolio/{id}', [PortofolioController::class, 'read']);

// Merchandise
Route::post('create-forum', [ForumController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-forum/{id}', [ForumController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-forum/{id}', [ForumController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-forum/{id}', [ForumController::class, 'read']);

// Komen
Route::post('create-komen/{idKomik}', [KomenController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('create-subKomen/{idKomen}/{idKomik}', [KomenController::class, 'createKomenBalasan'])->middleware('StudentOsisAdmin');
Route::post('update-forum/{id}', [ForumController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-forum/{id}', [ForumController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-forum/{id}', [ForumController::class, 'read']);