<?php

namespace Database\Factories;

use App\Models\Advertisement;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdvertisementsFile>
 */
class AdvertisementFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'advertisements_id' => Advertisement::inRandomOrder()->first()->id,
            'file_id'           => File::inRandomOrder()->first()->id,
            'is_main'           => false,
        ];
    }
}
