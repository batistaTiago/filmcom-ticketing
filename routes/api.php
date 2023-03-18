<?php

use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\TheaterRoomController;
use App\Http\Controllers\TheaterController;
use App\Http\Controllers\TicketTypeController;
use Illuminate\Support\Facades\Route;


Route::prefix('films')->group(function () {
    Route::get('/', [FilmController::class, 'index'])->name('api.films.index');
    Route::post('/create', [FilmController::class, 'store'])->name('api.films.create');

    Route::prefix('exhibitions')->group(function () {
        Route::get('/', [ExhibitionController::class, 'index'])->name('api.film_exhibitions.index');

        Route::prefix('{exhibition_id}')->group(function () {
            Route::get('/ticket-types', [ExhibitionController::class, 'getTicketTypes'])->name('api.exhibition-ticket-types.index');
        });
    });
});

Route::prefix('theaters')->group(function () {
    Route::post('/create', [TheaterController::class, 'store'])->name('api.theaters.create');

    Route::prefix('rooms')->group(function () {
        Route::get('/{room_id}', [TheaterRoomController::class, 'show'])->name('api.theater-rooms.show');
        Route::get('/{room_id}/{exhibition_id}', [TheaterRoomController::class, 'showAvailability'])->name('api.theater-rooms.show-availability');
        Route::post('import-seat-map', [TheaterRoomController::class, 'importSeatMapSpreadsheet'])->name('api.theater-room-seat-map.import');
    });
});

Route::prefix('exhibitions')->group(function () {
    Route::post('/create', [ExhibitionController::class, 'store'])->name('api.exhibitions.create');
    Route::patch('/{exhibition_id}', [ExhibitionController::class, 'update'])->name('api.exhibitions.update');
});

Route::prefix('ticket-types')->group(function () {
    Route::get('/', [TicketTypeController::class, 'index'])->name('api.ticket-types.index');
});
