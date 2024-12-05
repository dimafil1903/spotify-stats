<?php

namespace App\Http\Controllers\API\Spotify;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use App\Enums\Spotify\SpotifyTimeRange;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShareRequest;
use App\Services\Spotify\TrackShareService;
use Illuminate\Http\JsonResponse;

class SpotifyShareTracksController extends Controller
{
    public function __construct(
        private readonly TrackShareService $trackShareService
    )
    {
    }

    /**
     * @throws SpotifyApiException
     */
    public function createShareLink(CreateShareRequest $request): JsonResponse
    {
        $response = $this->trackShareService->createShareLink(
            $request->user(),
            $request->enum('time_range', SpotifyTimeRange::class)
        );

        return response()->json($response);
    }

    public function getSharedTracks(string $shareToken): JsonResponse
    {
        $response = $this->trackShareService->getSharedTracks($shareToken);

        return response()->json($response);
    }
}
