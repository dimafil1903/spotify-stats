<?php

namespace App\Services\Spotify\Clients;

use Aerni\Spotify\Exceptions\SpotifyAuthException;
use App\Models\User;
use Auth;

class SpotifyTokenContext
{
    /**
     * @throws SpotifyAuthException
     */
    public function getCurrentUserToken(): string
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->spotify_token) {
            throw new SpotifyAuthException('No valid Spotify token found');
        }

        return $user->spotify_token;
    }

    /**
     * @throws SpotifyAuthException
     */
    public function getRefreshToken(): string
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->spotify_refresh_token) {
            throw new SpotifyAuthException('No valid Spotify refresh token found');
        }

        return $user->spotify_refresh_token;
    }

    public function getExpiresAt(): int
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->spotify_token_expires_in;
    }
}
