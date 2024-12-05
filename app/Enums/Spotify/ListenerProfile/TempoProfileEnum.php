<?php

namespace App\Enums\Spotify\ListenerProfile;

enum TempoProfileEnum: string
{
    case FAST = 'Fast Paced';
    case UPBEAT = 'Upbeat';
    case MODERATE = 'Moderate';
    case SLOW = 'Slow & Steady';

    public static function fromTempo(float $tempo): self
    {
        return match (true) {
            $tempo >= 140 => self::FAST,
            $tempo >= 120 => self::UPBEAT,
            $tempo >= 90 => self::MODERATE,
            default => self::SLOW,
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::FAST => 'You enjoy fast-paced music.',
            self::UPBEAT => 'You enjoy upbeat music.',
            self::MODERATE => 'You enjoy moderate tempo music.',
            self::SLOW => 'You enjoy slow and steady music.',
        };
    }

    public function getEmoji(): string
    {
        return match ($this) {
            self::FAST => '⚡',
            self::UPBEAT => '🎵',
            self::MODERATE => '🎶',
            self::SLOW => '🐢',
        };
    }
}
