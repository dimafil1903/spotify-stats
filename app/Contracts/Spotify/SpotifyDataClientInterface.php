<?php

namespace App\Contracts\Spotify;

use App\Data\Spotify\TopTracksData;
use App\Enums\Spotify\SpotifyTimeRange;
use App\Models\User;

interface SpotifyDataClientInterface
{
    public function forUser(User $user): self;

    public function getTopTracks(
        SpotifyTimeRange $timeRange = SpotifyTimeRange::MEDIUM_TERM,
        int              $limit = 20,
        int              $offset = 0

    ): TopTracksData;
//    public function getTopArtists(array $params = []): TopArtistsData;
//    public function getUserProfile(): UserProfileData;
//    public function getRecentlyPlayed(array $params = []): RecentlyPlayedData;
}
