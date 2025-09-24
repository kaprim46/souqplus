<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'logo',
        'ad_id',
        'read',
    ];

    /**
     * Get the user that owns the UserNotification
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sender that owns the UserNotification
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the ad that owns the UserNotification
     * @return BelongsTo
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class, 'ad_id');
    }
}
