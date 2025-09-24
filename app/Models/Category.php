<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_robots'
    ];


    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($category) {
            $slug = Str::slug($category->name, '-');
            $originalSlug = $slug;
            $attempt = 1;
            $maxAttempts = 10;

            while (Category::query()->where('slug', $slug)->exists() && $attempt <= $maxAttempts) {
                $slug = $originalSlug . '-' . Str::random(5);
                $attempt++;
            }

            if (Category::query()->where('slug', $slug)->exists()) {
                // Handle the case where we still found a duplicate slug after max attempts
                throw new \Exception('Unable to generate a unique slug');
            }

            $category->slug = $slug;
        });
    }

    /**
     * Category Filters
     * @return belongsToMany
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'category_filters', 'category_id', 'filter_id');
    }


    /**
     * Sub Categories
     * @return HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }
}
