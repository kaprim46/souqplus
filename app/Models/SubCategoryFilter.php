<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategoryFilter extends Model
{
    use HasFactory;

    public $timestamps  = false;
    protected $fillable = ['sub_category_id', 'filter_id'];

    /**
     * Get the filter that owns the SubCategoryFilter
     * @return BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the filter that owns the SubCategoryFilter
     * @return BelongsTo
     */
    public function filter(): BelongsTo
    {
        return $this->belongsTo(Filter::class);
    }
}
