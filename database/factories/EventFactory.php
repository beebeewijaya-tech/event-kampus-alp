<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role' => 'admin']),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'poster_img' => 'posters/default.jpg',
            'event_date' => now()->addDays(7),
            'registration_deadline' => now()->addDays(5),
            'status' => 'open',
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => 'closed']);
    }
}
