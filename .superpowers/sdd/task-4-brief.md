# Task 4 Brief — Notifikasi Page

## Project context

Campus event registration app. Laravel 13, PHP 8.3, SQLite.
Work dir: `/Users/beewijaya/Development/Laravel/event-kampus-alp`

Layout: `resources/views/layouts/app.blade.php` (use `<x-layouts.app>`)

DB schema:
- `notifications`: id, user_id, registration_id, type(confirm|reminder), message, status, read_at(nullable), timestamps

Models:
- `App\Models\Notification` — `user()` belongsTo, `registration()` hasOne, `isRead()` returns bool (`read_at !== null`)
- `App\Models\User` — `notifications()` hasMany Notification

Routes already defined:
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
});
```

## What to implement

### 1. NotificationController

Create `app/Http/Controllers/NotificationController.php`.

**index()**:
```php
$notifications = auth()->user()
    ->notifications()
    ->latest()
    ->get();
return view('notifications.index', compact('notifications'));
```

**markRead(Notification $notification)**:
- Check the notification belongs to auth user: `abort_if($notification->user_id !== auth()->id(), 403)`
- Set read_at: `$notification->update(['read_at' => now()])`
- Redirect back: `return back()`

### 2. Notification list view — notifications/index.blade.php

Create `resources/views/notifications/index.blade.php`.

Use `<x-layouts.app>`.

Title: "Notifikasi"

List of notifications. For each notification:
- Unread = highlighted background (e.g. `bg-blue-50` or `bg-yellow-50`)
- Read = normal background (`bg-white`)
- Show: type badge (confirm = green "Konfirmasi", reminder = blue "Pengingat"), message text, timestamp (`created_at->diffForHumans()`)
- If unread: show a "Tandai Dibaca" button as a small POST form to `route('notifications.read', $notification)`

If empty: show "Tidak ada notifikasi."

Show a "Tandai Semua Dibaca" link or button is NOT needed — just per-item mark-read is fine.

## Imports needed

```php
use App\Models\Notification;
```

## Deliverables

Files to create:
- `app/Http/Controllers/NotificationController.php`
- `resources/views/notifications/index.blade.php`

## Testing

Run `php artisan test` — all existing tests must pass.

## Commit

Single commit: `feat: notifications page`

Do NOT add Co-Authored-By or AI attribution.

## Report

Write to `.superpowers/sdd/task-4-report.md`.
Return: status (DONE or BLOCKED), one-line summary, report path.
