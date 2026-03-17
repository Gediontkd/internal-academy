<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+2 hours');

        return [
            'created_by' => \App\Models\User::factory()->create(['role' => 'admin'])->id,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'start_time' => $start,
            'end_time' => $end,
            'capacity' => $this->faker->numberBetween(5, 30),
        ];
    }
}
