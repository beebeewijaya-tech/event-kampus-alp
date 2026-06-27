<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(['Regular', 'VIP', 'Online', 'Student']),
            'quota' => fake()->numberBetween(10, 200),
            'price' => fake()->randomElement([0, 50000, 100000, 150000]),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
