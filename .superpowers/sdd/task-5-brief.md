# Task 5 Brief — Admin: Daftar Peserta + Check-in

## Project context

Campus event registration app. Laravel 13, PHP 8.3, SQLite.
Work dir: `/Users/beewijaya/Development/Laravel/event-kampus-alp`

Admin layout: `resources/views/layouts/admin.blade.php` (use `<x-layouts.admin>`)
Regular layout: `resources/views/layouts/app.blade.php` (use `<x-layouts.app>`)

DB schema:
- `events`: id, user_id, title, description, poster_img, event_date, registration_deadline, status
- `event_categories`: id, event_id, name, quota, price, description(nullable)
- `registrations`: id, user_id, event_categories_id, check_in_code(unique), checked_in_at(nullable), status(pending|confirmed|waiting_list)

Models:
- `App\Models\Event` — `categories()` hasMany EventCategory
- `App\Models\EventCategory` — `registrations()` hasMany Registration, `event()` belongsTo
- `App\Models\Registration` — `user()` belongsTo User, `eventCategory()` belongsTo EventCategory (FK: event_categories_id), `isCheckedIn()` bool
- `App\Models\User`

RegistrationObserver is already set up in AppServiceProvider. When a Registration is DELETED, it automatically promotes the oldest waiting_list registration to confirmed and creates a notification.

Routes already defined in routes/web.php:
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('events/{event}/participants', [\App\Http\Controllers\Admin\ParticipantController::class, 'index'])->name('events.participants');
    Route::post('events/{event}/participants/{registration}/checkin', [\App\Http\Controllers\Admin\ParticipantController::class, 'checkin'])->name('events.checkin');
    Route::delete('events/{event}/participants/{registration}', [\App\Http\Controllers\Admin\ParticipantController::class, 'destroy'])->name('events.participants.destroy');
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    // ...also has resource route for events
});
```

## What to implement

### 1. Admin\DashboardController (minimal stub)

Create `app/Http/Controllers/Admin/DashboardController.php`:

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $totalRegistrations = Registration::count();
        $confirmedRegistrations = Registration::where('status', 'confirmed')->count();
        $recentEvents = Event::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalEvents', 'totalRegistrations', 'confirmedRegistrations', 'recentEvents'));
    }
}
```

### 2. Admin\ParticipantController

Create `app/Http/Controllers/Admin/ParticipantController.php`:

**index(Event $event)**:
```php
$confirmed = $event->registrations()->with('user', 'eventCategory')
    ->where('status', 'confirmed')
    ->get();
$waitingList = $event->registrations()->with('user', 'eventCategory')
    ->where('status', 'waiting_list')
    ->get();
return view('admin.participants.index', compact('event', 'confirmed', 'waitingList'));
```

Wait — `$event->registrations()` uses hasManyThrough. Let me clarify:
Actually use this approach to get registrations for the event:
```php
$confirmed = Registration::whereHas('eventCategory', fn($q) => $q->where('event_id', $event->id))
    ->where('status', 'confirmed')
    ->with('user', 'eventCategory')
    ->get();
$waitingList = Registration::whereHas('eventCategory', fn($q) => $q->where('event_id', $event->id))
    ->where('status', 'waiting_list')
    ->with('user', 'eventCategory')
    ->get();
```

**checkin(Event $event, Registration $registration)**:
- Verify registration belongs to this event: `abort_if($registration->eventCategory->event_id !== $event->id, 403)`
- Set checked_in_at: `$registration->update(['checked_in_at' => now()])`
- Return redirect back with success: `return back()->with('success', 'Check-in berhasil.')`

**destroy(Event $event, Registration $registration)**:
- Verify: `abort_if($registration->eventCategory->event_id !== $event->id, 403)`
- Delete: `$registration->delete()` (Observer fires automatically)
- Return redirect back with success

### 3. Admin dashboard view — admin/dashboard.blade.php

Create `resources/views/admin/dashboard.blade.php`.

Use `<x-layouts.admin>`.

Show 3 stat cards: Total Events, Total Registrations, Confirmed Registrations.
Below that: "Event Terbaru" table showing last 5 events (title, date, status).

### 4. Admin participants view — admin/participants/index.blade.php

Create `resources/views/admin/participants/index.blade.php`.

Use `<x-layouts.admin>`.

Title: "Peserta: {{ $event->title }}"

Use Alpine.js for two tabs: "Peserta Terkonfirmasi" and "Waiting List".

Tab structure with Alpine.js:
```html
<div x-data="{ tab: 'confirmed' }">
    <div>
        <button @click="tab = 'confirmed'" :class="tab === 'confirmed' ? 'border-b-2 border-blue-500' : ''">
            Peserta ({{ count($confirmed) }})
        </button>
        <button @click="tab = 'waiting'" :class="tab === 'waiting' ? 'border-b-2 border-blue-500' : ''">
            Waiting List ({{ count($waitingList) }})
        </button>
    </div>

    <div x-show="tab === 'confirmed'">
        <!-- confirmed table -->
    </div>
    <div x-show="tab === 'waiting'">
        <!-- waiting list table -->
    </div>
</div>
```

Confirmed table columns: Nama, Email, Kategori, Kode Check-in, Status Check-in, Aksi
- Status check-in: if `checked_in_at` is set show "Sudah Check-in (date)" else "Belum"
- Aksi: 
  - Check-in button (POST form to `route('admin.events.checkin', [$event, $registration])`) — only show if not yet checked in
  - Hapus button (DELETE form with `@method('DELETE')`, onclick confirm dialog) to `route('admin.events.participants.destroy', [$event, $registration])`

Waiting list table columns: Nama, Email, Kategori, Tanggal Daftar, Aksi
- Aksi: Hapus button (DELETE form)

## Deliverables

Files to create:
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/ParticipantController.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/participants/index.blade.php`

Make sure the `Admin` directory exists: `app/Http/Controllers/Admin/`

## Testing

Run `php artisan test` — all tests must pass.

## Commit

Single commit: `feat: admin dashboard and participant management with check-in`

Do NOT add Co-Authored-By or AI attribution.

## Report

Write to `.superpowers/sdd/task-5-report.md`.
Return: status (DONE or BLOCKED), one-line summary, report path.
