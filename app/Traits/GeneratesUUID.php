<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUUID
{
    protected static function bootGeneratesUUID(): void
    {
        static::creating(function ($model) {
            $uuidField = $model->getUUIDFieldName();

            if (empty($model->$uuidField)) {
                while (true) {
                    $uuid = Str::uuid();
                    if (!self::where($uuidField, $uuid)->exists()) {
                        $model->$uuidField = $uuid;
                        break;
                    }
                }
            }
        });
    }

    protected function getUUIDFieldName()
    {
        return property_exists($this, 'uuidField') ? $this->uuidField : 'uuid';
    }
}
