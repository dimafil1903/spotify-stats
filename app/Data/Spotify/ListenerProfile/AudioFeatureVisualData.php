<?php

namespace App\Data\Spotify\ListenerProfile;

use Spatie\LaravelData\Data;

class AudioFeatureVisualData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly float $value,
        public readonly string $color,
        public readonly string $description,
    ) {}
}
