<?php

use App\Http\Controllers\Api\PenjualanDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

Route::post('/register', \App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/register1', \App\Http\Controllers\Api\RegisterController::class)->name('register1');

Route::post('/login', \App\Http\Controllers\Api\LoginController::class)->name('login');

Route::post('/logout', \App\Http\Controllers\Api\LogoutController::class)->name('logout');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
    ]);
});

Route::get('/levels', [\App\Http\Controllers\Api\LevelController::class, 'index']);
Route::post('/levels', [\App\Http\Controllers\Api\LevelController::class, 'store']);
Route::post('/levels/{level}', [\App\Http\Controllers\Api\LevelController::class, 'show']);
Route::put('/levels/{level}', [\App\Http\Controllers\Api\LevelController::class, 'update']);
Route::delete('/levels/{level}', [\App\Http\Controllers\Api\LevelController::class, 'destroy']);

Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
Route::post('/users', [\App\Http\Controllers\Api\UserController::class, 'store']);
Route::post('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'show']);
Route::put('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'update']);
Route::delete('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);

Route::get('/kategori', [\App\Http\Controllers\Api\KategoriController::class, 'index']);
Route::post('/kategori', [\App\Http\Controllers\Api\KategoriController::class, 'store']);
Route::post('/kategori/{kategori}', [\App\Http\Controllers\Api\KategoriController::class, 'show']);
Route::put('/kategori/{kategori}', [\App\Http\Controllers\Api\KategoriController::class, 'update']);
Route::delete('/kategori/{kategori}', [\App\Http\Controllers\Api\KategoriController::class, 'destroy']);

Route::get('/barang', [\App\Http\Controllers\Api\BarangController::class, 'index']);
Route::post('/barang', [\App\Http\Controllers\Api\BarangController::class, 'store']);
Route::post('/barang/{barang}', [\App\Http\Controllers\Api\BarangController::class, 'show']);
Route::put('/barang/{barang}', [\App\Http\Controllers\Api\BarangController::class, 'update']);
Route::delete('/barang/{barang}', [\App\Http\Controllers\Api\BarangController::class, 'destroy']);

Route::get('/penjualan-detail/barang/{barang_id}', [PenjualanDetailController::class, 'getByBarang']);
