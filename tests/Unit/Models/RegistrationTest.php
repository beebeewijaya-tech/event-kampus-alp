<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_in_code_auto_generated_on_create(): void
    {
        $registration = $this->makeRegistration();

        $this->assertNotNull($registration->check_in_code);
        $this->assertEquals(8, strlen($registration->check_in_code));
        $this->assertEquals(strtoupper($registration->check_in_code), $registration->check_in_code);
    }

    public function test_check_in_code_is_unique(): void
    {
        $r1 = $this->makeRegistration();
        $r2 = $this->makeRegistration();

        $this->assertNotEquals($r1->check_in_code, $r2->check_in_code);
    }

    public function test_is_checked_in_false_when_null(): void
    {
        $registration = $this->makeRegistration(['checked_in_at' => null]);

        $this->assertFalse($registration->isCheckedIn());
    }

    public function test_is_checked_in_true_when_timestamp_set(): void
    {
        $registration = $this->makeRegistration(['checked_in_at' => now()]);

        $this->assertTrue($registration->isCheckedIn());
    }

    private function makeRegistration(array $overrides = []): Registration
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::factory()->create(['user_id' => $admin->id]);
        $category = EventCategory::factory()->create(['event_id' => $event->id]);
        $user = User::factory()->create();

        return Registration::factory()->create(array_merge([
            'user_id' => $user->id,
            'event_categories_id' => $category->id,
        ], $overrides));
    }
}
