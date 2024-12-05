<?php

namespace App\Enums\Spotify\ListenerProfile;

enum MoodProfileEnum: string
{
    case ENERGETIC_POSITIVE = 'Energetic & Positive';
    case OPTIMISTIC = 'Optimistic';
    case BALANCED = 'Balanced';
    case MELANCHOLIC = 'Melancholic';
    case INTROSPECTIVE = 'Introspective';

    public function getDescription(): string
    {
        return match($this) {
            self::ENERGETIC_POSITIVE => 'Your music choices radiate positivity and energy!',
            self::OPTIMISTIC => 'You tend to gravitate towards uplifting music.',
            self::BALANCED => 'You appreciate both the highs and lows in music.',
            self::MELANCHOLIC => 'You connect with emotionally deep and thoughtful music.',
            self::INTROSPECTIVE => 'You prefer music that makes you think and feel deeply.'
        };
    }

    public function getEmoji(): string
    {
        return match($this) {
            self::ENERGETIC_POSITIVE => '⚡',
            self::OPTIMISTIC => '☀️',
            self::BALANCED => '☯️',
            self::MELANCHOLIC => '🌙',
            self::INTROSPECTIVE => '🤔'
        };
    }

    public static function fromFeatures(float $valence, float $energy): self
    {
        return match(true) {
            $valence >= 0.7 && $energy >= 0.7 => self::ENERGETIC_POSITIVE,
            $valence >= 0.6 => self::OPTIMISTIC,
            $valence >= 0.4 => self::BALANCED,
            $valence >= 0.3 => self::MELANCHOLIC,
            default => self::INTROSPECTIVE,
        };
    }
}
