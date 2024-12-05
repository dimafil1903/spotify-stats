<?php

namespace App\Services\Spotify\ListenerProfile;

use App\Data\Spotify\ListenerProfile\AudioFeaturesData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;

class ProfileAnalyzer
{
    public function analyzeFeatures(Collection $features): AudioFeaturesData
    {
        if ($features->isEmpty()) {
            return new AudioFeaturesData(
                danceability: 0,
                energy: 0,
                valence: 0,
                instrumentalness: 0,
                acousticness: 0,
                tempo: 0,
                speechiness: 0,
                id: 'average'
            );
        }

        $totals = [
            'danceability' => 0,
            'energy' => 0,
            'valence' => 0,
            'instrumentalness' => 0,
            'acousticness' => 0,
            'tempo' => 0,
            'speechiness' => 0,
        ];

        foreach ($features as $feature) {
            // Transform array to AudioFeaturesData if needed
            $featureData = is_array($feature)
                ? AudioFeaturesData::fromArray($feature)
                : $feature;

            $totals['danceability'] += $featureData->danceability;
            $totals['energy'] += $featureData->energy;
            $totals['valence'] += $featureData->valence;
            $totals['instrumentalness'] += $featureData->instrumentalness;
            $totals['acousticness'] += $featureData->acousticness;
            $totals['tempo'] += $featureData->tempo;
            $totals['speechiness'] += $featureData->speechiness;
        }

        $count = $features->count();

        return new AudioFeaturesData(
            danceability: $totals['danceability'] / $count,
            energy: $totals['energy'] / $count,
            valence: $totals['valence'] / $count,
            instrumentalness: $totals['instrumentalness'] / $count,
            acousticness: $totals['acousticness'] / $count,
            tempo: $totals['tempo'] / $count,
            speechiness: $totals['speechiness'] / $count,
            id: 'average'
        );
    }
}
