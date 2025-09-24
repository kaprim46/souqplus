<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementView extends Model
{
    protected $fillable = [
        'advertisement_id',
        'user_id',
        'ip'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }
}
