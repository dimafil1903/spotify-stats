<?php

namespace App\Data\Spotify;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;

class ArtistData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
    )
    {
    }

    public function getSpotifyUrl(): string
    {
        return $this->externalUrls['spotify'] ?? '';
    }

    public function transform(bool $transformValues = true, WrapExecutionType $wrapExecutionType = WrapExecutionType::Disabled, bool $mapPropertyNames = true): array
    {
        return [
            ...parent::transform(),
            'spotifyUrl' => $this->getSpotifyUrl(),
        ];
    }
}
