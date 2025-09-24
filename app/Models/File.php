<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'mime_type',
        'size',
        'path',
        'file_name',
        'ext',
        'type',
        'user_id'
    ];

    public $appends = [
        'url',
        'url_thumb'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = auth()->id();
        });
    }

    public function getUrlAttribute(): string
    {
        return asset($this->path . '/' . $this->file_name);
    }

    public function getUrlThumbAttribute(): string
    {
        return asset($this->path .
            '/thumbs/' . $this->file_name);
    }
}
