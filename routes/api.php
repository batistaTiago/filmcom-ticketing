<?php

use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\SeatMapImportController;
use App\Http\Controllers\TheaterRoomController;
use App\Http\Controllers\TheaterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TicketTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('films')->group(function () {
    Route::get('/', [FilmController::class, 'index'])->name('api.films.index');
    Route::post('/', [FilmController::class, 'store'])->name('api.films.create');

    Route::prefix('exhibitions')->group(function () {
        Route::get('/', [ExhibitionController::class, 'index'])->name('api.film_exhibitions.index');

        Route::prefix('{exhibition_id}')->group(function () {
            Route::get('/ticket-types', [ExhibitionController::class, 'getTicketTypes'])->name('api.exhibition-ticket-types.index');
        });
    });
});

Route::prefix('theaters')->group(function () {
    Route::post('/', [TheaterController::class, 'store'])->name('api.theaters.create');
    Route::get('/', [TheaterController::class, 'index'])->name('api.theaters.index');

    Route::prefix('rooms')->group(function () {
        Route::post('/', [TheaterRoomController::class, 'store'])->name('api.theater-rooms.create');
        Route::get('/availability/{exhibition_id}', [TheaterRoomController::class, 'showAvailability'])->name('api.theater-rooms.show-availability');
        Route::get('/{room_id}', [TheaterRoomController::class, 'show'])->name('api.theater-rooms.show');
    });
});

Route::prefix('exhibitions')->group(function () {
    Route::post('/', [ExhibitionController::class, 'store'])->name('api.exhibitions.create');
    Route::patch('/{exhibition_id}', [ExhibitionController::class, 'update'])->name('api.exhibitions.update');
});

Route::prefix('ticket-types')->group(function () {
    Route::get('/', [TicketTypeController::class, 'index'])->name('api.ticket-types.index');
});

Route::prefix('seat-map')->group(function () {
    Route::get('download-example', [SeatMapImportController::class, 'getSeatMapExampleSpreadsheet'])->name('api.theater-room-seat-map.download-example');
    Route::post('import', [SeatMapImportController::class, 'importSeatMapSpreadsheet'])->name('api.theater-room-seat-map.import');
});

Route::prefix('cart')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('add-ticket', [CartController::class, 'addTicket'])->name('api.cart.add-ticket');
        Route::post('remove-ticket', [CartController::class, 'removeTicket'])->name('api.cart.remove-ticket');
    });
});
