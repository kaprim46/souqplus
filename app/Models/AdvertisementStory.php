<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementStory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'advertisement_id'];

    /**
     * Get the user that owns the AdvertisementStory
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the advertisement that owns the AdvertisementStory
     * @return BelongsTo
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Views of the story
     */
    public function views(): BelongsTo
    {
        return $this->belongsTo(AdvertisementStoryView::class, 'id', 'advertisement_story_id');
    }
}
