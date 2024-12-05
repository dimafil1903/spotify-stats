<?php

namespace App\Data\Spotify\ListenerProfile;

use Spatie\LaravelData\Data;

class AudioFeaturesData extends Data
{
    public function __construct(
        public readonly float $danceability,
        public readonly float $energy,
        public readonly float $valence,
        public readonly float $instrumentalness,
        public readonly float $acousticness,
        public readonly float $tempo,
        public readonly float $speechiness,
        public readonly string $id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            danceability: (float) ($data['danceability'] ?? 0),
            energy: (float) ($data['energy'] ?? 0),
            valence: (float) ($data['valence'] ?? 0),
            instrumentalness: (float) ($data['instrumentalness'] ?? 0),
            acousticness: (float) ($data['acousticness'] ?? 0),
            tempo: (float) ($data['tempo'] ?? 0),
            speechiness: (float) ($data['speechiness'] ?? 0),
            id: (string) ($data['id'] ?? 'unknown')
        );
    }
}
