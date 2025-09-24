<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementFilter extends Model
{
    use HasFactory;

    public $timestamps  = false;
    protected $fillable = ['advertisement_id', 'filter_id', 'value'];



    /**
     * Get the advertisement that owns the AdvertisementFilter
     *
     * @return BelongsTo
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Get the filter that owns the AdvertisementFilter
     *
     * @return BelongsTo
     */
    public function filter(): BelongsTo
    {
        return $this->belongsTo(Filter::class);
    }
}
