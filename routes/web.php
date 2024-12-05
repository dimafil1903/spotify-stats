<?php

use App\Http\Controllers\API\Spotify\SpotifyAuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/shared/{token}', function () {
    return Inertia::render('SharedTracks', [
        'token' => request()->route('token')
    ]);
})->name('shared.tracks');


Route::get('/auth/spotify', [SpotifyAuthController::class, 'redirect']);
Route::get('/auth/spotify/callback', [SpotifyAuthController::class, 'callback'])->name('spotify.callback');


