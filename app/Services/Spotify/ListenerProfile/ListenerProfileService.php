<?php

namespace App\Services\Spotify\ListenerProfile;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Aerni\Spotify\Exceptions\SpotifyAuthException;
use App\Data\Spotify\ListenerProfile\ListenerProfileData;
use App\Enums\Spotify\ListenerProfile\DanceProfileEnum;
use App\Enums\Spotify\ListenerProfile\MoodProfileEnum;
use App\Enums\Spotify\ListenerProfile\SoundProfileEnum;
use App\Enums\Spotify\ListenerProfile\TempoProfileEnum;
use App\Enums\Spotify\SpotifyTimeRange;
use App\Services\Spotify\Clients\SpotifyDataClient;
use Cache;
use Carbon\CarbonInterval;

readonly class ListenerProfileService
{
    public function __construct(
        private SpotifyDataClient $spotifyClient,
        private ProfileAnalyzer   $analyzer,
    )
    {
    }

    public function generateProfile(SpotifyTimeRange $timeRange = SpotifyTimeRange::MEDIUM_TERM): ListenerProfileData
    {
        return Cache::remember(
            key: "listener_profile_{$timeRange->value}_" . auth()->id(),
            ttl: CarbonInterval::hour(),
            callback: fn() => $this->buildProfile($timeRange)
        );
    }

    /**
     * @throws SpotifyApiException
     * @throws SpotifyAuthException
     */
    private function buildProfile(SpotifyTimeRange $timeRange = SpotifyTimeRange::MEDIUM_TERM): ListenerProfileData
    {
        $tracks = $this->spotifyClient->getTopTracks(
            timeRange: $timeRange,
            limit: 100,
            paginate: true
        );

        $features = $this->spotifyClient->getAudioFeatures(collect($tracks)->pluck('id')->toArray());

        $averageFeatures = $this->analyzer->analyzeFeatures($features);

        return new ListenerProfileData(
            danceProfile: DanceProfileEnum::fromFeatures(
                $averageFeatures->danceability,
                $averageFeatures->energy
            ),
            moodProfile: MoodProfileEnum::fromFeatures(
                $averageFeatures->valence,
                $averageFeatures->energy
            ),
            soundProfile: SoundProfileEnum::fromFeatures(
                $averageFeatures->acousticness,
                $averageFeatures->instrumentalness,
                $averageFeatures->speechiness
            ),
            tempoProfile: TempoProfileEnum::fromTempo($averageFeatures->tempo),
            averageFeatures: $averageFeatures,
        );
    }
}
