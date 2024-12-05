<?php

use App\Http\Controllers\API\Spotify\ListenerProfileController;
use App\Http\Controllers\API\Spotify\SpotifyDataController;
use App\Http\Controllers\API\Spotify\SpotifyShareTracksController;
use Illuminate\Support\Facades\Route;

// routes/api.php

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/spotify/top-tracks', [SpotifyDataController::class, 'topTracks'])->name('spotify.top-tracks');
    Route::post('/spotify/share', [SpotifyShareTracksController::class, 'createShareLink']);
    Route::get('/spotify/listener-profile', [ListenerProfileController::class, 'show'])->name('spotify.listener-profile');
//    Route::get('/spotify/top-artists', [SpotifyDataController::class, 'getTopArtists'])->name('spotify.top-artists');
//    Route::get('/spotify/genre-stats', [SpotifyDataController::class, 'getGenreStats'])->name('spotify.genre-stats');
//    Route::get('/spotify/listening-history', [SpotifyDataController::class, 'getListeningHistory'])->name('spotify.listening-history');
});

Route::get('/spotify/shared/{token}', [SpotifyShareTracksController::class, 'getSharedTracks']);
