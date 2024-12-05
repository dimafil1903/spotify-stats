<?php

namespace App\Data\Spotify;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;

class AlbumData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public array $images,
    ) {}

    public function getCoverUrl(): ?string
    {
        return $this->images[0]->url ?? null;
    }

    public function transform(bool $transformValues = true, WrapExecutionType $wrapExecutionType = WrapExecutionType::Disabled, bool $mapPropertyNames = true): array
    {
        return [
            ...parent::transform(),
            'coverUrl' => $this->getCoverUrl(),
        ];
    }
}
