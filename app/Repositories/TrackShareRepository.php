<?php

namespace App\Repositories;

use App\Models\TrackShare;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TrackShareRepository
{
    public function create(array $data): TrackShare
    {
        return TrackShare::create($data);
    }

    public function findValidByToken(string $token): TrackShare
    {
        $trackShare = TrackShare::where('share_token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->with('user')
            ->first();

        if (!$trackShare) {
            throw new ModelNotFoundException('Shared tracks not found or link has expired');
        }

        return $trackShare;
    }
}
