<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => 'password', // password_hash('password', PASSWORD_DEFAULT),
            'bio' => $this->faker->sentence,
            'role' => $this->faker->randomElement(['admin', 'business', 'customer']),
            'country_code' => $this->faker->countryCode,
            'phone_number' => $this->faker->phoneNumber,
            'phone_number_verified_at' => now(),
            'avatar' => null,
            'is_verified' => $this->faker->boolean,
            'business_info' => $this->faker->sentence,
            'fcm_token' => $this->faker->uuid,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
