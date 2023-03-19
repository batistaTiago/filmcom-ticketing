<?php

use Illuminate\Support\Facades\Route;

Route::get('/theaters', fn () => view('theaters'))->name('theaters.index');
Route::get('/films', fn () => view('films'))->name('films.index');
Route::get('/film_exhibitions/{film_id}', fn () => view('film_exhibitions'))->name('film_exhibitions.index');

Route::get('seat_map/{room_id}', fn () => view('seat_map'));
Route::get('exhibition_seat_map/{exhibition_id}', fn () => view('exhibition_seat_map'));
