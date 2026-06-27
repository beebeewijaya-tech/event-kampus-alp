<?php

namespace Database\Factories;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'registration_id' => Registration::factory(),
            'type' => fake()->randomElement(['confirm', 'reminder']),
            'message' => fake()->sentence(),
            'status' => 'pending',
            'read_at' => null,
        ];
    }
}
