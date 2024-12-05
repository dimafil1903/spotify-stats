<?php

namespace App\Data\Spotify\ListenerProfile;


use App\Enums\Spotify\ListenerProfile\DanceProfileEnum;
use App\Enums\Spotify\ListenerProfile\MoodProfileEnum;
use App\Enums\Spotify\ListenerProfile\SoundProfileEnum;
use App\Enums\Spotify\ListenerProfile\TempoProfileEnum;
use Spatie\LaravelData\Data;

class ListenerProfileData extends Data
{
    public function __construct(
        public readonly DanceProfileEnum  $danceProfile,
        public readonly MoodProfileEnum   $moodProfile,
        public readonly SoundProfileEnum  $soundProfile,
        public readonly TempoProfileEnum  $tempoProfile,
        public readonly AudioFeaturesData $averageFeatures,
    )
    {
    }

    public function toPresentation(): array
    {
        return [
            'summary' => $this->generateSummary(),
            'sections' => [
                'mood' => $this->getMoodSection(),
                'sound' => $this->getSoundSection(),
                'dance' => $this->getDanceSection(),
                'tempo' => $this->getTempoSection(),
            ],
            'features' => $this->getAudioFeatures(),
            'charts' => $this->getChartData(),
        ];
    }

    private function generateSummary(): string
    {
        return sprintf(
            "You're a %s who enjoys %s music with a %s vibe at an %s pace.",
            $this->danceProfile->value,
            strtolower($this->soundProfile->value),
            strtolower($this->moodProfile->value),
            strtolower($this->tempoProfile->value)
        );
    }

    private function getMoodSection(): array
    {
        return [
            'title' => 'Mood Profile',
            'value' => $this->moodProfile->value,
            'description' => $this->moodProfile->getDescription(),
            'emoji' => $this->moodProfile->getEmoji(),
            'icon' => 'heart',
            'color' => '#ec4899'
        ];
    }

    private function getSoundSection(): array
    {
        return [
            'title' => 'Sound Profile',
            'value' => $this->soundProfile->value,
            'description' => $this->soundProfile->getDescription(),
            'emoji' => $this->soundProfile->getEmoji(),
            'icon' => 'music',
            'color' => '#6366f1'
        ];
    }

    private function getDanceSection(): array
    {
        return [
            'title' => 'Dance Style',
            'value' => $this->danceProfile->value,
            'description' => $this->danceProfile->getDescription(),
            'emoji' => $this->danceProfile->getEmoji(),
            'icon' => 'user',
            'color' => '#22c55e'
        ];
    }

    private function getTempoSection(): array
    {
        return [
            'title' => 'Tempo Profile',
            'value' => $this->tempoProfile->value,
            'description' => $this->tempoProfile->getDescription(),
            'emoji' => $this->tempoProfile->getEmoji(),
            'icon' => 'activity',
            'color' => '#eab308'
        ];
    }

    private function getAudioFeatures(): array
    {
        return [
            [
                'name' => 'Danceability',
                'value' => $this->averageFeatures->danceability,
                'color' => '#22c55e',
                'description' => self::formatPercentage($this->averageFeatures->danceability) . '% danceable'
            ],
            [
                'name' => 'Energy',
                'value' => $this->averageFeatures->energy,
                'color' => '#ef4444',
                'description' => self::formatPercentage($this->averageFeatures->energy) . '% energetic'
            ],
            [
                'name' => 'Positivity',
                'value' => $this->averageFeatures->valence,
                'color' => '#eab308',
                'description' => self::formatPercentage($this->averageFeatures->valence) . '% positive'
            ],
            [
                'name' => 'Instrumentalness',
                'value' => $this->averageFeatures->instrumentalness,
                'color' => '#6366f1',
                'description' => self::formatPercentage($this->averageFeatures->instrumentalness) . '% instrumental'
            ],
            [
                'name' => 'Acousticness',
                'value' => $this->averageFeatures->acousticness,
                'color' => '#8b5cf6',
                'description' => self::formatPercentage($this->averageFeatures->acousticness) . '% acoustic'
            ],
            [
                'name' => 'Tempo',
                'value' => $this->averageFeatures->tempo / 200, // Normalize to 0-1 range
                'color' => '#ec4899',
                'description' => round($this->averageFeatures->tempo) . ' BPM'
            ],
        ];
    }

    private function getChartData(): array
    {
        return [
            'radar' => [
                'labels' => ['Danceability', 'Energy', 'Positivity', 'Instrumentalness', 'Acousticness'],
                'datasets' => [
                    [
                        'data' => [
                            $this->averageFeatures->danceability,
                            $this->averageFeatures->energy,
                            $this->averageFeatures->valence,
                            $this->averageFeatures->instrumentalness,
                            $this->averageFeatures->acousticness,
                        ],
                        'backgroundColor' => 'rgba(29, 185, 84, 0.3)',
                        'borderColor' => 'rgb(29, 185, 84)',
                        'pointBackgroundColor' => '#1DB954',
                    ]
                ]
            ]
        ];
    }

    private static function formatPercentage(float $value): int
    {
        return (int)round($value * 100);
    }
}
