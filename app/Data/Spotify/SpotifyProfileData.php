<?php

namespace App\Data\Spotify;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Carbon\Carbon;

class SpotifyProfileData extends Data
{
    public function __construct(
        public string $id,

        #[MapName('display_name')]
        public string $displayName,

        public ?string $email,

        public array $images,

        public ?string $country,

        public ?string $product,

        #[MapName('explicit_content')]
        public ?object $explicitContent,

        public object $followers,

        public ?string $uri,

        #[MapName('external_urls')]
        public object $externalUrls,
    ) {}

    public static function rules(): array
    {
        return [
            'id' => ['required', 'string'],
            'display_name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'images' => ['array'],
            'images.*.url' => ['required', 'string', 'url'],
            'images.*.height' => ['nullable', 'integer'],
            'images.*.width' => ['nullable', 'integer'],
            'country' => ['nullable', 'string'],
            'product' => ['nullable', 'string'],
            'explicit_content' => ['nullable', 'object'],
            'followers' => ['required', 'object'],
            'uri' => ['nullable', 'string'],
            'external_urls' => ['required', 'object'],
        ];
    }

    public function getAvatarUrl(): ?string
    {
        return $this->images[0]['url'] ?? null;
    }

    public function getSpotifyUrl(): ?string
    {
        return $this->externalUrls->spotify ?? null;
    }

    public function getFollowersCount(): int
    {
        return $this->followers->total ?? 0;
    }
}
