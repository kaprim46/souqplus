<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExploreSlider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'link',
        'image',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/explore_sliders/' . $this->image) : null;
    }
}
