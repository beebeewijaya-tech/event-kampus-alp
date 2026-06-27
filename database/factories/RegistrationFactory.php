<?php

namespace Database\Factories;

use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_categories_id' => EventCategory::factory(),
            'checked_in_at' => null,
            'status' => 'confirmed',
        ];
    }

    public function waitingList(): static
    {
        return $this->state(['status' => 'waiting_list']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
