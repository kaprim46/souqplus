<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteAdvertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisements_id',
        'user_id',
    ];

    /**
     * Get the advertisement that owns the FavoriteAdvertisement
     *
     * @return BelongsTo
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class, 'advertisements_id');
    }

    /**
     * Get the user that owns the FavoriteAdvertisement
     *
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
