<?php

namespace App\Data\Spotify;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;

class TrackData extends Data
{
    public function __construct(
        public string         $id,
        public string         $name,
        public string         $uri,
        public AlbumData      $album,
        #[DataCollectionOf(ArtistData::class)]
        public DataCollection $artists,
        public int            $popularity,
        #[MapName('preview_url')]
        public ?string        $previewUrl,
        #[MapName('duration_ms')]
        public int            $durationMs,
        public bool           $explicit,
        #[MapName('external_urls')]
        public object         $externalUrls,
        #[MapName('external_ids')]
        public object          $externalIds,
        #[MapName('disc_number')]
        public int            $discNumber,
        #[MapName('track_number')]
        public int            $trackNumber,
        #[MapName('is_playable')]
        public bool           $isPlayable,
        public string         $type,
        #[MapName('is_local')]
        public bool           $isLocal,

    )
    {
    }

    public static function fromSpotifyTrack(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            uri: $data['uri'],
            album: AlbumData::from($data['album']),
            artists: ArtistData::collection($data['artists']),
            popularity: $data['popularity'],
            previewUrl: $data['preview_url'] ?? null,
            durationMs: $data['duration_ms'],
            explicit: $data['explicit'],
            externalUrls: $data['external_urls'],
            externalIds: $data['external_ids'],
            discNumber: $data['disc_number'],
            trackNumber: $data['track_number'],
            isPlayable: $data['is_playable'] ?? true,
            type: $data['type'],
            isLocal: $data['is_local'] ?? false,
        );
    }

    public function getSpotifyUrl(): string
    {
        return $this->externalUrls?->spotify ?? '';
    }

    public function getDurationFormatted(): string
    {
        $seconds = floor($this?->durationMs / 1000);
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }



    public function getMainArtistName(): string
    {
        return $this->artists->first()?->name ?? 'Unknown Artist';
    }

    public function transform(bool $transformValues = true, WrapExecutionType $wrapExecutionType = WrapExecutionType::Disabled, bool $mapPropertyNames = true): array
    {
        return [
            ...parent::transform(),
            'albumCover' => $this->album->getCoverUrl(),
            'duration' => [
                'ms' => $this->durationMs,
                'formatted' => $this->getDurationFormatted(),
            ],
            'mainArtist' => $this->getMainArtistName(),
            'spotifyUrl' => $this->getSpotifyUrl(),
        ];
    }
}
