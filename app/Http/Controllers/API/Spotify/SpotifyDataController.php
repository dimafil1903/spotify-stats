<?php

namespace App\Http\Controllers\API\Spotify;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Aerni\Spotify\Exceptions\SpotifyAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Spotify\TopItemsRequest;
use App\Services\Spotify\Clients\SpotifyDataClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SpotifyDataController extends Controller
{
    public function __construct(
        private readonly SpotifyDataClient $spotifyClient
    )
    {
    }

    /**
     * Get user's top tracks
     * @throws SpotifyApiException|SpotifyAuthException
     */
    public function topTracks(TopItemsRequest $request): JsonResponse
    {

        $tracks = $this->spotifyClient->getTopTracks(
            timeRange: $request->timeRange(),
            offset: $request->offset()
        );

        return response()->json([
            'data' => $tracks,
            'meta' => [
                'time_range' => $request->timeRange(),
                'current_page' => $request->page(),
            ],
        ]);
    }


}
