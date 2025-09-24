<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'status',
        'icon'
    ];


    protected $casts = [
        'status' => 'string',
    ];

    public $appends = ['icon_url'];

    /**
     * Boot the model.
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($ads) {
            while (true) {
                $slug = Str::slug($ads->name, '-');
                if (Page::query()->where('slug', $slug)->exists()) {
                    $ads->slug = $slug . '-' . Str::random(5);
                } else {
                    $ads->slug = $slug;
                    break;
                }
            }
        });
    }

    public function getIconUrlAttribute(): ?string // <-- 2. Change return type to allow null
    {
        // 3. THE FIX: Use Storage::url() to generate the correct path
        if ($this->icon) {
            // This will correctly generate a URL like: /storage/pages/your-image.jpg
            return Storage::disk('public')->url('pages/' . $this->icon);
        }

        // Return null if there's no icon, which is better for the frontend
        return null;
    }
}
