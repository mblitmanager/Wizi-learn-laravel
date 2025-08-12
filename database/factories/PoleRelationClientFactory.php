<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PoleRelationClient>
 */
class PoleRelationClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => 'pole_relation_client',
            'user_id' => \App\Models\User::factory(),
            'prenom' => $this->faker->firstName,
            'telephone' => $this->faker->phoneNumber,
        ];
    }
}
