<?php

namespace App\Services\Spotify\Clients;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Aerni\Spotify\Exceptions\SpotifyAuthException;
use App\Data\Spotify\ListenerProfile\AudioFeaturesData;
use App\Data\Spotify\SpotifyProfileData;
use App\Data\Spotify\TrackData;
use App\Enums\Spotify\SpotifyTimeRange;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;
use SpotifyWebAPI\SpotifyWebAPI;
use Throwable;

class SpotifyDataClient
{
    private const API_BASE_URL = 'https://api.spotify.com/v1';
    private const CACHE_PREFIX = 'spotify_data_';
    private const DEFAULT_CACHE_TTL = 3600; // 1 hour
    private const CACHE_TTL_SHORT = 300; // 5 minutes
    private ?string $currentToken = null;

    private SpotifyWebAPI $api;

    public function __construct(
        private readonly SpotifyTokenContext $tokenContext,
        private readonly SpotifyAuthClient   $authClient,
        private readonly int                 $maxRetries = 3,
        private readonly int                 $retryDelay = 100,
    )
    {
        $this->api = new SpotifyWebAPI();
    }

    public function setAccessToken(string $token): void
    {
        $this->currentToken = $token;
        $this->api->setAccessToken($token);
    }

    /**
     * @throws SpotifyAuthException
     */
    private function ensureValidToken(): void
    {
        if (!$this->currentToken) {
//            if token is expired, refresh it
            if ($this->tokenContext->getExpiresAt() < now()->timestamp) {
                $this->authClient->refreshToken($this->tokenContext->getRefreshToken());
            }
            $this->setAccessToken($this->tokenContext->getCurrentUserToken());
        }
    }

    /**
     * @throws SpotifyApiException
     * @throws SpotifyAuthException
     * @throws Throwable
     */
    public function me(): SpotifyProfileData
    {
        $this->ensureValidToken();
        $response = $this->executeApiCall(fn() => $this->api->me());
        return SpotifyProfileData::from($response);
    }

    /**
     * @throws SpotifyApiException
     * @throws SpotifyAuthException
     */
    public function getTopTracks(
        SpotifyTimeRange $timeRange,
        int              $limit = 50,
        int              $offset = 0,
        bool             $paginate = false
    ): DataCollection
    {
        $this->ensureValidToken();

        if (!$paginate) {
            return $this->fetchTopTracks($timeRange, $limit, $offset);
        }

        return $this->cached("top_tracks_paginated_{$timeRange->value}_{$limit}", function () use ($timeRange, $limit) {
            $allTracks = [];
            $offset = 0;
            $batchSize = 50;

            while ($offset < $limit) {
                $currentBatchSize = min($batchSize, $limit - $offset);
                $batch = $this->fetchTopTracks($timeRange, $currentBatchSize, $offset);

                if (empty($batch->items())) {
                    break;
                }

                $allTracks = [...$allTracks, ...$batch->items()];
                $offset += $batchSize;
            }

            return TrackData::collection($allTracks);
        });
    }

    private function fetchTopTracks(SpotifyTimeRange $timeRange, int $limit, int $offset): DataCollection
    {
        return $this->cached(
            "top_tracks_{$timeRange->value}_{$limit}_{$offset}",
            function () use ($timeRange, $limit, $offset) {
                $response = $this->executeApiCall(fn() => $this->api->getMyTop('tracks', [
                    'time_range' => $timeRange->value,
                    'limit' => $limit,
                    'offset' => $offset,
                ]));
                return TrackData::collection($response->items);
            }
        );
    }

    /**
     * @throws SpotifyAuthException
     * @throws SpotifyApiException
     */
    public function getAudioFeatures(array $trackIds): Collection
    {
        if (empty($trackIds)) {
            return collect();
        }

        $this->ensureValidToken();

        return $this->cached("audio_features_" . md5(implode(',', $trackIds)), function () use ($trackIds) {
            $features = [];
            $chunks = array_chunk($trackIds, 100);

            foreach ($chunks as $chunk) {
                $response = $this->executeApiCall(
                    fn() => $this->api->getAudioFeatures($chunk)
                );

                foreach ($response->audio_features as $feature) {
                    $features[] = AudioFeaturesData::fromArray((array)$feature);
                }
            }

            return collect($features);
        });
    }

    /**
     * @throws SpotifyApiException
     * @throws SpotifyAuthException
     */
    public function getRecentlyPlayed(int $limit = 50): DataCollection
    {
        $this->ensureValidToken();

        return $this->cached('recently_played', function () use ($limit) {
            $response = $this->executeRequest('GET', '/me/player/recently-played', [
                'limit' => $limit
            ]);

            return TrackData::collection(
                Collection::make($response['items'])
                    ->pluck('track')
                    ->all()
            );
        }, self::CACHE_TTL_SHORT);
    }

    /**
     * @throws Throwable
     * @throws SpotifyApiException
     */
    private function executeApiCall(callable $apiCall): mixed
    {
        try {
            return retry($this->maxRetries, function () use ($apiCall) {
                try {
                    return $apiCall();
                } catch (\Exception $e) {
                    if ($this->shouldRetry($e)) {
                        throw $e;
                    }
                    throw new SpotifyApiException($e->getMessage(), $e->getCode(), $e);
                }
            }, $this->retryDelay);
        } catch (\Exception $e) {
            throw new SpotifyApiException(
                "Spotify API call failed: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    private function executeRequest(string $method, string $endpoint, array $params = []): array
    {
        try {
            return retry($this->maxRetries, function () use ($method, $endpoint, $params) {
                return $this->createRequest()
                    ->$method(self::API_BASE_URL . $endpoint, $params)
                    ->throw()
                    ->json();
            }, $this->retryDelay);
        } catch (RequestException $e) {
            throw new SpotifyApiException(
                "Request to Spotify API failed: {$e->getMessage()}",
                $e->response?->status() ?? 500,
                $e
            );
        }
    }

    /**
     * @throws SpotifyAuthException
     */
    private function createRequest(): PendingRequest
    {
        return Http::withToken($this->tokenContext->getCurrentUserToken())
            ->withHeaders(['Accept' => 'application/json'])
            ->timeout(10)
            ->retry($this->maxRetries, $this->retryDelay, fn($exception) => $this->shouldRetry($exception));
    }

    private function shouldRetry(\Exception $exception): bool
    {
        if ($exception instanceof RequestException) {
            $status = $exception->response?->status();
            return in_array($status, [429, 500, 502, 503, 504]);
        }
        return false;
    }

    private function cached(string $key, callable $callback, ?int $ttl = null): mixed
    {
        if (auth()->user()) {
            $key = auth()->id() . '_' . $key;
        }

        try {
            return Cache::remember(
                self::CACHE_PREFIX . $key,
                $ttl ?? self::DEFAULT_CACHE_TTL,
                $callback
            );
        } catch (SpotifyAuthException|SpotifyApiException $e) {
            Cache::forget(self::CACHE_PREFIX . $key);
            throw $e;
        } catch (\Exception $e) {
            throw new SpotifyApiException(
                "Failed to fetch cached data: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    public function clearCache(?string $key = null): void
    {
        if (auth()->user()) {
            $key = auth()->id() . '_' . $key;
        }

        if ($key) {
            Cache::forget(self::CACHE_PREFIX . $key);
            return;
        }

        $keys = [
            'profile',
            'top_tracks_short_term',
            'top_tracks_medium_term',
            'top_tracks_long_term',
            'recently_played'
        ];

        foreach ($keys as $cacheKey) {
            Cache::forget(self::CACHE_PREFIX . $cacheKey);
        }
    }
}
