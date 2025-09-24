<?php

namespace Database\Factories;

use App\Models\Advertisement;
use App\Models\AdvertisementsFile;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertisement>
 */
class AdvertisementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->paragraph,
            'slug' => $this->faker->unique()->slug,
            'category_id' => $this->faker->numberBetween(1, 2),
            'sub_category_id' => $this->faker->numberBetween(1, 2),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'country_code' => $this->faker->countryCode,
            'phone_number' => $this->faker->phoneNumber,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'status' => 'approved',
            'reject_reason' => $this->faker->optional()->sentence,
            'user_id' => $this->faker->numberBetween(1, 15),
            'custom_fields' => [],
            'country_id' => $this->faker->optional()->numberBetween(1, 2),
            'city_id' => $this->faker->optional()->numberBetween(1, 2),
        ];
    }

    public function configure(): AdvertisementFactory
    {
        return $this->afterCreating(function (Advertisement $advertisement) {
            $fileCount = rand(5, 10);
            $files = File::inRandomOrder()->take($fileCount)->get();

            foreach ($files as $index => $file) {
                AdvertisementsFile::create([
                    'advertisements_id' => $advertisement->id,
                    'file_id' => $file->id,
                    'is_main' => $index === 0,
                ]);
            }
        });
    }
}
