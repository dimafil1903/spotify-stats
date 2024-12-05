<?php

namespace App\Services\Spotify\Clients;

use Aerni\Spotify\Exceptions\SpotifyAuthException;
use SpotifyWebAPI\Session;
use App\Data\Spotify\Auth\SpotifyTokenData;
use Illuminate\Support\Facades\Cache;

class SpotifyAuthClient
{
    private Session $session;

    public function __construct()
    {
        $this->session = new Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            route('spotify.callback')
        );
    }

    public function getAuthorizationUrl(): string
    {
        $scopes = [
            'user-read-recently-played',
            'user-top-read',
            'user-read-currently-playing',
            'user-read-playback-state',
            'user-library-read',
            'user-read-private',
            'user-read-email',
        ];

        return $this->session->getAuthorizeUrl([
            'scope' => $scopes,
            'show_dialog' => true,
        ]);
    }

    public function handleCallback(string $code): SpotifyTokenData
    {
        try {
            $this->session->requestAccessToken($code);

            return SpotifyTokenData::from([
                'access_token' => $this->session->getAccessToken(),
                'refresh_token' => $this->session->getRefreshToken(),
                'expires_in' => $this->session->getTokenExpiration(),
                'token_type' => 'Bearer',
                'scope' => implode(' ', $this->session->getScope() ?? []),
            ]);
        } catch (\Exception $e) {
            throw new SpotifyAuthException("Failed to get access token: {$e->getMessage()}");
        }
    }

    public function refreshToken(string $refreshToken): SpotifyTokenData
    {
        try {
            $this->session->refreshAccessToken($refreshToken);

            $tokenData = SpotifyTokenData::from([
                'access_token' => $this->session->getAccessToken(),
                'refresh_token' => $this->session->getRefreshToken(),
                'expires_in' => $this->session->getTokenExpiration(),
                'token_type' => 'Bearer',
                'scope' => implode(' ', $this->session->getScope() ?? []),
            ]);

            if (auth()->check()) {
                auth()->user()->update([
                    'spotify_token' => $tokenData->accessToken,
                    'spotify_refresh_token' => $tokenData->refreshToken,
                    'spotify_token_expires_in' => $tokenData->expiresIn,
                ]);
            }

            return $tokenData;
        } catch (\Exception $e) {
            throw new SpotifyAuthException("Failed to refresh token: {$e->getMessage()}");
        }
    }
}
