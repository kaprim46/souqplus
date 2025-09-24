<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_robots',
        'category_id',
    ];


    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($category) {
            $slug = Str::slug($category->name, '-');
            $originalSlug = $slug;
            $attempt = 1;
            $maxAttempts = 10;

            while (SubCategory::query()->where('slug', $slug)->exists() && $attempt <= $maxAttempts) {
                $slug = $originalSlug . '-' . Str::random(5);
                $attempt++;
            }

            if (SubCategory::query()->where('slug', $slug)->exists()) {
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
        return $this->belongsToMany(Filter::class, 'sub_category_filters', 'sub_category_id', 'filter_id');
    }

    /**
     * Main Category Relation
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the sub sub categories for the sub category
     */
    public function subSubCategories(): HasMany
    {
        return $this->hasMany(SubSubCategory::class);
    }

    /**
     * Get the advertisements for the sub category
     */

}
