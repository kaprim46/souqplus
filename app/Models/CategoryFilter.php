<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryFilter extends Model
{
    use HasFactory;

    public $timestamps  = false;
    protected $table    = 'category_filters';
    protected $fillable = ['category_id', 'filter_id'];

    /**
     * Get the category that owns the category filter.
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the filter that owns the category filter.
     * @return BelongsTo
     */
    public function filter(): BelongsTo
    {
        return $this->belongsTo(Filter::class);
    }
}
