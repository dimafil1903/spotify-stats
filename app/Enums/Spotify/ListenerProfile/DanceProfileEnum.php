<?php

namespace App\Enums\Spotify\ListenerProfile;

enum DanceProfileEnum: string
{
    case PARTY_LOVER = 'Party Lover';
    case RHYTHM_ENTHUSIAST = 'Rhythm Enthusiast';
    case CASUAL_DANCER = 'Casual Dancer';
    case CALM_LISTENER = 'Calm Listener';

    public function getDescription(): string
    {
        return match ($this) {
            self::PARTY_LOVER => 'You love high-energy, danceable tracks that get everyone moving!',
            self::RHYTHM_ENTHUSIAST => 'You appreciate a good beat and aren\'t afraid to show it.',
            self::CASUAL_DANCER => 'You enjoy dancing but also appreciate mellower moments.',
            self::CALM_LISTENER => 'You prefer to take in the music rather than dance to it.'
        };
    }

    public function getEmoji(): string
    {
        return match ($this) {
            self::PARTY_LOVER => '🎉',
            self::RHYTHM_ENTHUSIAST => '🎵',
            self::CASUAL_DANCER => '💃',
            self::CALM_LISTENER => '🎧'
        };
    }

    public static function fromFeatures(float $danceability, float $energy): self
    {
        return match (true) {
            $danceability >= 0.7 && $energy >= 0.7 => self::PARTY_LOVER,
            $danceability >= 0.6 && $energy >= 0.6 => self::RHYTHM_ENTHUSIAST,
            $danceability >= 0.5 => self::CASUAL_DANCER,
            default => self::CALM_LISTENER,
        };
    }
}
