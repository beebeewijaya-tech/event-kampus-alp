# Task 2 Brief — Daftar Event + Detail Event

## Project context

Campus event registration app. Laravel 13, PHP 8.3, SQLite.
Work dir: `/Users/beewijaya/Development/Laravel/event-kampus-alp`

Layouts already exist:
- `resources/views/layouts/app.blade.php` — use this with `<x-layouts.app>` component

DB schema:
- `events`: id, user_id, title, description, poster_img, event_date, registration_deadline, status(open|closed), timestamps
- `event_categories`: id, event_id, name, quota, price, description(nullable), timestamps
- `registrations`: id, user_id, event_categories_id, check_in_code, checked_in_at(nullable), status(pending|confirmed|waiting_list), timestamps

Models:
- `App\Models\Event` — `scopeOpen()`, `categories()` hasMany EventCategory
- `App\Models\EventCategory` — `availableSlots()` returns (quota - confirmed_count), `isFull()` bool, `event()` belongsTo
- `App\Models\Registration`

Routes already defined in `routes/web.php`:
```php
Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');
```

## What to implement (keep it simple)

### 1. EventController

Create `app/Http/Controllers/EventController.php` with two methods:

**index()**: Get all open events (use `Event::open()->latest()->paginate(12)`), return view `events.index` with `$events`.

**show(Event $event)**: Load event with its categories (eager load `categories` with confirmed registration count), return view `events.show` with `$event`.

For the show method, load categories and attach confirmed count:
```php
$event->load('categories');
foreach ($event->categories as $category) {
    $category->confirmed_count = $category->registrations()->where('status', 'confirmed')->count();
}
```

### 2. Daftar Event view — events/index.blade.php

Create `resources/views/events/index.blade.php`.

Use `<x-layouts.app>` as wrapper.

Show a simple grid of event cards. Each card:
- Poster image (if `poster_img` exists: `<img src="{{ Storage::url($event->poster_img) }}">`; else a gray placeholder div)
- Title
- Event date (formatted `d M Y`)
- Status badge (open = green, closed = red)
- "Lihat Detail" button → `route('events.show', $event)`

Use `{{ $events->links() }}` for pagination at the bottom.

### 3. Detail Event view — events/show.blade.php

Create `resources/views/events/show.blade.php`.

Use `<x-layouts.app>` as wrapper.

Show:
- Event poster (if exists) or placeholder
- Title, description
- Event date, registration deadline
- Status badge
- Table of categories: columns Name, Quota, Available Slots, Price, Action
  - Available slots = `$category->quota - $category->confirmed_count`
  - If `$category->confirmed_count >= $category->quota`: show "Waiting List" label instead of slot count and the register button should say "Daftar Waiting List"
  - Action: if user is logged in AND event status is 'open': show "Daftar" button → `route('registrations.create', ['event' => $event, 'category' => $category->id])` (we'll use query param for pre-selecting category)
  - If not logged in: show "Login untuk Daftar" → `route('login')`
  - If event is closed: show "Pendaftaran Ditutup" text

## Storage facade

Add `use Illuminate\Support\Facades\Storage;` in EventController if needed.

## Deliverables

Files to create:
- `app/Http/Controllers/EventController.php`
- `resources/views/events/index.blade.php`
- `resources/views/events/show.blade.php`

## Testing

Run `php artisan test` — all existing tests must pass.

## Commit

Single commit: `feat: event list and detail pages`

Do NOT add Co-Authored-By or any AI attribution to the commit message.

## Report

Write report to `.superpowers/sdd/task-2-report.md`.

Return: status (DONE or BLOCKED), one-line summary, report path.
