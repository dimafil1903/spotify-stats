<?php

namespace App\Services\Spotify;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Aerni\Spotify\Exceptions\SpotifyAuthException;
use App\DTOs\Responses\Spotify\SharedTracksResponseDTO;
use App\DTOs\Responses\Spotify\ShareLinkResponseDTO;
use App\Enums\Spotify\SpotifyTimeRange;
use App\Models\TrackShare;
use App\Models\User;
use App\Services\Spotify\Clients\SpotifyDataClient;
use Carbon\Carbon;
use Str;

readonly class TrackShareService
{
    public function __construct(
        private SpotifyDataClient $spotifyClient
    )
    {
    }

    /**
     * @throws SpotifyApiException
     * @throws SpotifyAuthException
     */
    public function createShareLink(User $user, SpotifyTimeRange $timeRange = SpotifyTimeRange::MEDIUM_TERM): array
    {
        $existingShares = TrackShare::where('user_id', $user->id)->count();

        if ($existingShares >= 50) {
            TrackShare::where('user_id', $user->id)
                ->oldest()
                ->first()
                ->delete();
        }

        $tracks = $this->spotifyClient->getTopTracks(
            timeRange: $timeRange,
        );

        $share = TrackShare::create([
            'user_id' => $user->id,
            'token' => Str::random(32),
            'time_range' => $timeRange,
            'tracks_data' => $tracks->toArray(),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return [
            'share_url' => route('shared.tracks', $share->token),
            'expires_at' => $share->expires_at,
        ];
    }

    public function getSharedTracks(string $token): array
    {
        $share = TrackShare::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return [
            'tracks' => $share->tracks_data,
            'user' => [
                'name' => $share->user->name,
                'avatar' => $share->user->avatar,
            ],
            'time_range' => $share->time_range,
            'expires_at' => $share->expires_at,
        ];
    }
}
