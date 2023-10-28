<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\KomenController;
use App\Http\Controllers\Api\KomenForumController;
use App\Http\Controllers\Api\KomikController;
use App\Http\Controllers\Api\MerchandiseController;
use App\Http\Controllers\Api\NPCController;
use App\Http\Controllers\Api\PortofolioController;
use App\Http\Controllers\Api\ReviewLayananController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubKomikController;
use App\Http\Controllers\Api\TransaksiLayananController;
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
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);
Route::post('recover', [AuthController::class, 'recover'])->name('recover');
Route::get('verifyRegister/{verification_code}', [AuthController::class, 'verifyUser'])->name('verifyRegister');
Route::post('resetPassword/{uuid}', [AuthController::class, 'resetPassword'])->name('resetPassword');



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
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
Route::get('show-all-comic', [KomikController::class, 'getAll'])->middleware('Admin');


// SubKomik
Route::post('create-subkomik/{id}', [SubKomikController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-subkomik/{uuid}', [SubKomikController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-subkomik/{id}', [SubKomikController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-subkomik/{id}', [SubKomikController::class, 'read']);
Route::get('show-all-subcomic/{id}', [SubKomikController::class, 'getAll'])->middleware('Admin');



// NPC
Route::post('create-npc', [NPCController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-npc/{uuid}', [NPCController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-npc/{uuid}', [NPCController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-npc/{uuid}', [NPCController::class, 'read']);
Route::get('show-all-npc', [NPCController::class, 'getAll'])->middleware('Admin');

// Merchandise
Route::post('create-merchandise', [MerchandiseController::class, 'create'])->middleware('Admin');
Route::post('update-merchandise/{id}', [MerchandiseController::class, 'update'])->middleware('Admin');
Route::delete('delete-merchandise/{id}', [MerchandiseController::class, 'delete'])->middleware('Admin');
Route::post('read-merchandise/{id}', [MerchandiseController::class, 'read']);
Route::get('show-all-merchandise', [MerchandiseController::class, 'getAll'])->middleware('Admin');


// Portofolio
Route::post('create-portfolio', [PortofolioController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-portfolio/{id}', [PortofolioController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-portfolio/{id}', [PortofolioController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-portfolio/{id}', [PortofolioController::class, 'read']);
Route::get('show-all-portfolio', [PortofolioController::class, 'getAll'])->middleware('Admin');


// Forum
Route::post('create-forum', [ForumController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('update-forum/{id}', [ForumController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-forum/{uuid}', [ForumController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-forum/{id}', [ForumController::class, 'read']);
Route::get('show-all-forum', [ForumController::class, 'getAll'])->middleware('StudentOsisAdmin');


// Anncounmenet
Route::post('create-announcement', [AnnouncementController::class, 'create'])->middleware('OsisAdmin');
Route::post('update-announcement/{id}', [AnnouncementController::class, 'update'])->middleware('OsisAdmin');
Route::delete('delete-announcement/{id}', [AnnouncementController::class, 'delete'])->middleware('OsisAdmin');
Route::post('read-announcement/{id}', [AnnouncementController::class, 'read']);
Route::get('show-all-announcement', [AnnouncementController::class, 'getAll'])->middleware('StudentOsisAdmin');

// Komen
Route::post('create-komen/{idKomik}', [KomenController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('create-subKomen/{idKomen}/{idKomik}', [KomenController::class, 'createKomenBalasan'])->middleware('StudentOsisAdmin');
Route::post('update-forum/{id}', [KomenController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-forum/{id}', [KomenController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-forum/{id}', [KomenController::class, 'read']);

// Komen Forum
Route::post('create-komenForum/{idForum}', [KomenForumController::class, 'create'])->middleware('StudentOsisAdmin');
Route::post('create-subKomen/{idKomen}/{idKomik}', [KomenForumController::class, 'createKomenBalasan'])->middleware('StudentOsisAdmin');
Route::post('update-forum/{id}', [KomenForumController::class, 'update'])->middleware('StudentOsisAdmin');
Route::delete('delete-forum/{id}', [KomenForumController::class, 'delete'])->middleware('StudentOsisAdmin');
Route::post('read-forum/{id}', [KomenForumController::class, 'read']);
Route::get('show-all-komenForum', [KomenForumController::class, 'getAll'])->middleware('StudentOsisAdmin');


// TransaksiLayanan
Route::post('create-transaksiLayanan/{idServicer}', [TransaksiLayananController::class, 'create'])->middleware('allRole');
Route::post('update-transkasiLayanan/{id}', [TransaksiLayananController::class, 'update'])->middleware('allRole');
Route::delete('delete-transaksiLayanan/{id}', [TransaksiLayananController::class, 'delete'])->middleware('allRole');
Route::post('read-transaksiLayanan/{id}', [TransaksiLayananController::class, 'read']);

// ReviewLayanan
Route::post('create-reviewLayanan/{idTransaksiLayanan}', [ReviewLayananController::class, 'create'])->middleware('allRole');
Route::post('update-reviewLayanan/{id}', [ReviewLayananController::class, 'update'])->middleware('allRole');
Route::delete('delete-reviewLayanan/{id}', [ReviewLayananController::class, 'delete'])->middleware('allRole');
Route::post('read-reviewLayanan/{id}', [ReviewLayananController::class, 'read']);
