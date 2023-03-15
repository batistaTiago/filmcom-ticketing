<?php

use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\TheaterRoomController;
use App\Http\Controllers\TheaterController;
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

Route::prefix('films')->group(function () {
    Route::get('/', [FilmController::class, 'index'])->name('api.films.index');
    Route::post('/create', [FilmController::class, 'store'])->name('api.films.create');

    Route::prefix('exhibitions')->group(function () {
        Route::get('/', [ExhibitionController::class, 'index'])->name('api.film_exhibitions.index');
    });
});

Route::prefix('theaters')->group(function () {
    Route::post('/create', [TheaterController::class, 'store'])->name('api.theaters.create');

    Route::prefix('rooms')->group(function () {
        Route::get('/{room_id}', [TheaterRoomController::class, 'show'])->name('api.theater-rooms.show');
    });
});

Route::prefix('exhibitions')->group(function () {
    Route::post('/create', [ExhibitionController::class, 'store'])->name('api.exhibitions.create');
});
