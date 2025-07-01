<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commercial>
 */
class CommercialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => 'commercial',
            'user_id' => User::factory(),
            'prenom' => $this->faker->firstName,
            'telephone' => $this->faker->phoneNumber,
        ];
    }
}
