<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = ['follower_id', 'followed_id', 'follow_account_type'];

    /**
     * Get the user that is being followed.
     * @return BelongsTo
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }

    /**
     * Get the user that is following.
     * @return BelongsTo
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
