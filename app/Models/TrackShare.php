<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackShare extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'time_range',
        'tracks_data',
        'expires_at',
    ];

    protected $casts = [
        'tracks_data' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


