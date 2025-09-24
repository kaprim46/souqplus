<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $primaryKey = null;


    public $timestamps = false;


    public static function get(string $key, $default = null)
    {
        $setting = self::query()->where('key', $key)->first();

        if ($setting) {
            return $setting->value;
        }

        return $default;
    }

    public static function set(string $key, $value): void
    {
        $setting = self::query()->where('key', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            self::query()->create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
