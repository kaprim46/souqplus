<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementsFile extends Model
{
    use HasFactory;


    protected $fillable = [
        'advertisements_id',
        'file_id',
        'is_main'
    ];

    public $table = 'advertisements_files';
}
