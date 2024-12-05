<?php

namespace App\Data\Spotify\Auth;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class SpotifyTokenData extends Data
{
    public function __construct(
        #[MapName('access_token')]
        public string $accessToken,

        #[MapName('token_type')]
        public string $tokenType,

        #[MapName('expires_in')]
        public int $expiresIn,

        #[MapName('refresh_token')]
        public ?string $refreshToken,

        public ?string $scope,

        public Carbon $expiresAt,
    ) {}

    public static function fromApiResponse(array $data): self
    {
        return new self(
            accessToken: $data['access_token'],
            tokenType: $data['token_type'],
            expiresIn: $data['expires_in'],
            refreshToken: $data['refresh_token'] ?? null,
            scope: $data['scope'] ?? null,
            expiresAt: now()->addSeconds($data['expires_in'])
        );
    }
}

