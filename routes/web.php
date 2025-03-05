<?php

use App\Http\Controllers\BabyKidController;
use App\Http\Controllers\BeautynHealthController;
use App\Http\Controllers\FnBController;
use App\Http\Controllers\HomeCareController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', [HomeController::class, 'index']);

// Route::get('/food-beverage', [FnBController::class, 'fnb']);
// Route::get('/beauty-healthy', [BeautynHealthController::class, 'bnh']);
// Route::get('/home-care', [HomeCareController::class, 'homecare']);
// Route::get('/baby-kid', [BabyKidController::class, 'babykid']);

// Route::get('/user', [UserController::class, 'user']);
// Route::get('/sales', [SalesController::class, 'sales']);

Route::get('/', function () {
    return view('welcome');
});
Route::get('/level',  [LevelController::class, 'index']);
Route::get('/kategori',  [KategoriController::class, 'index']);
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/tambah', [UserController::class, 'tambah']);
Route::post('/user/tambah_simpan', [UserController::class, 'tambah_simpan']);
Route::get('/user/ubah/{id}', [UserController::class, 'ubah']);
Route::put('/user/ubah_simpan/{id}', [UserController::class, 'ubah_simpan']);
Route::get('/user/hapus/{id}', [UserController::class, 'hapus']);
