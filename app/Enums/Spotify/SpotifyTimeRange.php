<?php

namespace App\Enums\Spotify;

enum SpotifyTimeRange: string
{
    case SHORT_TERM = 'short_term';   // Last 4 weeks
    case MEDIUM_TERM = 'medium_term'; // Last 6 months
    case LONG_TERM = 'long_term';     // Calculated from all time

    public function label(): string
    {
        return match($this) {
            self::SHORT_TERM => 'Last 4 weeks',
            self::MEDIUM_TERM => 'Last 6 months',
            self::LONG_TERM => 'All time',
        };
    }
}

