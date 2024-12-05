<?php

namespace App\Http\Requests\Spotify;

use App\Enums\Spotify\SpotifyTimeRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TopItemsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'time_range' => ['sometimes', 'string', new Enum(SpotifyTimeRange::class)],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function timeRange(): SpotifyTimeRange
    {
        return $this->enum('time_range', SpotifyTimeRange::class);
    }

    public function page(): int
    {
        return $this->input('page', 1);
    }

    public function offset(): int
    {
        return ($this->page() - 1) * 50;
    }
}
