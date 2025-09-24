<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'options'];

    protected $casts = [
        'options' => 'array',
    ];


    /**
     * Get the category that owns the filter.
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the sub Category
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
}
