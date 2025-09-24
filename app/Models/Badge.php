<?php

namespace App\Models;

use App\Traits\GeneratesUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory, GeneratesUUID;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'condition_type',
        'condition',
    ];

    protected $casts = [
        'condition' => 'array',
    ];

    public $appends = ['icon_url'];

    public function getIconUrlAttribute(): string
    {
        return $this->icon ? asset('badges/' . $this->icon) : '';
    }
}
