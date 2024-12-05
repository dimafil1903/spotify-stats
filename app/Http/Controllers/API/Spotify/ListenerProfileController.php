<?php

namespace App\Http\Controllers\API\Spotify;

use App\Data\Spotify\ListenerProfile\ListenerProfileData;
use App\Enums\Spotify\SpotifyTimeRange;
use App\Http\Controllers\Controller;
use App\Services\Spotify\ListenerProfile\ListenerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListenerProfileController extends Controller
{
    public function __construct(
        private readonly ListenerProfileService $profileService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $this->profileService->generateProfile(
            $request->enum('time_range', SpotifyTimeRange::class)
        );

        return response()->json($profile->toPresentation());
    }
}
