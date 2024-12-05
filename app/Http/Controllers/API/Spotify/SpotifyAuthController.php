<?php

namespace App\Http\Controllers\API\Spotify;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Aerni\Spotify\Exceptions\SpotifyAuthException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Spotify\Clients\SpotifyAuthClient;
use App\Services\Spotify\Clients\SpotifyDataClient;
use Illuminate\Http\Request;

class SpotifyAuthController extends Controller
{
    public function __construct(
        private readonly SpotifyAuthClient $authClient,
        private readonly SpotifyDataClient $dataClient,
    )
    {
    }

    public function redirect()
    {
        return redirect($this->authClient->getAuthorizationUrl());
    }

    public function callback(Request $request)
    {
        try {
            if (!$request->has('code')) {
                throw new SpotifyAuthException('No code provided');
            }
            $tokenData = $this->authClient->handleCallback($request->code);
            $this->dataClient->setAccessToken($tokenData->accessToken);

            $spotifyProfile = $this->dataClient->me();

            $user = User::updateOrCreate(
                ['spotify_id' => $spotifyProfile->id],
                [
                    'name' => $spotifyProfile->displayName,
                    'email' => $spotifyProfile->email,
                    'avatar' => $spotifyProfile->images[0]->url ?? null,
                    'spotify_token' => $tokenData->accessToken,
                    'spotify_refresh_token' => $tokenData->refreshToken,
                    'spotify_token_expires_in' => $tokenData->expiresIn,
                ]
            );

            auth()->login($user);

            return redirect()->route('welcome')
                ->with('success', 'Successfully connected to Spotify');
        } catch (\Exception $e) {
            return redirect()->route('welcome')
                ->with('error', 'Failed to connect to Spotify');
        }
    }

}

