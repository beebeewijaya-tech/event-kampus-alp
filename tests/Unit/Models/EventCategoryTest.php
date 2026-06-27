<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_slots_returns_quota_minus_confirmed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::factory()->create(['user_id' => $admin->id]);
        $category = EventCategory::factory()->create(['event_id' => $event->id, 'quota' => 5]);

        $users = User::factory(3)->create();
        foreach ($users as $user) {
            Registration::factory()->create([
                'user_id' => $user->id,
                'event_categories_id' => $category->id,
                'status' => 'confirmed',
            ]);
        }

        $this->assertEquals(2, $category->availableSlots());
    }

    public function test_available_slots_ignores_waiting_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::factory()->create(['user_id' => $admin->id]);
        $category = EventCategory::factory()->create(['event_id' => $event->id, 'quota' => 2]);

        $confirmed = User::factory()->create();
        Registration::factory()->create([
            'user_id' => $confirmed->id,
            'event_categories_id' => $category->id,
            'status' => 'confirmed',
        ]);

        $waiting = User::factory()->create();
        Registration::factory()->create([
            'user_id' => $waiting->id,
            'event_categories_id' => $category->id,
            'status' => 'waiting_list',
        ]);

        $this->assertEquals(1, $category->availableSlots());
    }

    public function test_is_full_when_no_slots_remain(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::factory()->create(['user_id' => $admin->id]);
        $category = EventCategory::factory()->create(['event_id' => $event->id, 'quota' => 1]);

        Registration::factory()->create([
            'user_id' => User::factory()->create()->id,
            'event_categories_id' => $category->id,
            'status' => 'confirmed',
        ]);

        $this->assertTrue($category->isFull());
    }
}
