<?php

namespace App\Enums\Spotify\ListenerProfile;

enum SoundProfileEnum: string
{
    case LYRICS_FOCUSED = 'Lyrics Focused';
    case INSTRUMENTAL = 'Instrumental Enthusiast';
    case ACOUSTIC = 'Acoustic Lover';
    case ELECTRONIC = 'Electronic/Produced';

    public static function fromFeatures(
        float $acousticness,
        float $instrumentalness,
        float $speechiness
    ): self {
        return match(true) {
            $speechiness > 0.33 => self::LYRICS_FOCUSED,
            $instrumentalness > 0.5 => self::INSTRUMENTAL,
            $acousticness > 0.5 => self::ACOUSTIC,
            default => self::ELECTRONIC,
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::LYRICS_FOCUSED => 'You enjoy music with a strong lyrical focus.',
            self::INSTRUMENTAL => 'You enjoy music with a strong instrumental focus.',
            self::ACOUSTIC => 'You enjoy music with a strong acoustic focus.',
            self::ELECTRONIC => 'You enjoy music with a strong electronic or produced focus.',
        };
    }

    public function getEmoji(): string
    {
        return match ($this) {
            self::LYRICS_FOCUSED => '🎤',
            self::INSTRUMENTAL => '🎻',
            self::ACOUSTIC => '🎸',
            self::ELECTRONIC => '🎧',
        };
    }
}


