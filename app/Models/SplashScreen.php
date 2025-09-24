<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SplashScreen extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image'
    ];

    public $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}
