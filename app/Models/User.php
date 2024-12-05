<?php

namespace App\Models;

use App\Models\Concerns\HasSpotifyAuth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'spotify_id',
        'spotify_token',
        'spotify_refresh_token',
        'spotify_token_expires_in',
        'spotify_avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'spotify_token',
        'spotify_refresh_token',
    ];

    protected $casts = [
        'spotify_token_expires_in' => 'integer',
    ];
}
