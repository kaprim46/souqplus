<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementStoryView extends Model
{
    use HasFactory;

    protected $fillable = ['advertisement_story_id', 'user_id', 'ip_address'];

    /**
     * Get the story that owns the AdvertisementStoryView
     * @return BelongsTo
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(AdvertisementStory::class, 'advertisement_story_id');
    }

    /**
     * Get the user that owns the AdvertisementStoryView
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
