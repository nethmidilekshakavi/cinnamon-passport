<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'grower_id' => User::factory(),
            'plot' => 'P' . fake()->numberBetween(1, 5),
            'weight_kg' => fake()->randomFloat(2, 5, 80),
            'harvest_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'drying_days' => fake()->numberBetween(2, 7),
            'grade' => fake()->randomElement(['ALBA', 'C5', 'C4', 'H1']),
            'source' => 'app',
            'status' => 'recorded',
        ];
    }
}
