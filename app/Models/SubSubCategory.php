<?php

namespace App\Models;

use App\Traits\Slugify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSubCategory extends Model
{
    use HasFactory, SoftDeletes, Slugify;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'sub_category_id',
        'status'
    ];

    /**
     * Get the sub category that owns the sub sub category.
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the advertisements for the sub sub category
     */
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }
} 