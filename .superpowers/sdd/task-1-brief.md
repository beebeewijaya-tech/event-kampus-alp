# Task 1 Brief — Foundation: Layout, Middleware, Observer, Routes

## Project context

Campus event registration app. Laravel 13, PHP 8.3, SQLite, Tailwind 4 (via @tailwindcss/vite), Alpine.js (CDN).

Vite entry: `resources/css/app.css` (has `@import 'tailwindcss';`) and `resources/js/app.js` (empty).
Use `@vite(['resources/css/app.css', 'resources/js/app.js'])` in layouts.
Use Alpine.js via CDN script tag: `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>`

DB schema summary:
- `users`: id, name, email, phone(nullable), password, role(admin|peserta), timestamps
- `events`: id, user_id, title, description, poster_img, event_date, registration_deadline, status(open|closed), timestamps
- `event_categories`: id, event_id, name, quota, price, description(nullable), timestamps
- `registrations`: id, user_id, event_categories_id, check_in_code(unique), checked_in_at(nullable), status(pending|confirmed|waiting_list), timestamps
- `notifications`: id, user_id, registration_id, type(confirm|reminder), message, status, read_at(nullable), timestamps

Models already exist:
- `App\Models\User` — `isAdmin()` returns bool, `notifications()` hasMany
- `App\Models\Event`
- `App\Models\EventCategory`
- `App\Models\Registration`
- `App\Models\Notification`
- `App\Observers\RegistrationObserver` does NOT exist yet — you create it

## What to implement (keep it simple)

### 1. EnsureAdmin middleware

Create `app/Http/Middleware/EnsureAdmin.php`:
- If user not logged in → redirect to /login
- If user is logged in but `role !== 'admin'` → redirect to / with error flash "Akses ditolak."
- Otherwise → pass through

### 2. Register middleware alias in bootstrap/app.php

In the `->withMiddleware(function (Middleware $middleware)` block, add:
```php
$middleware->alias(['admin' => \App\Http\Middleware\EnsureAdmin::class]);
```

### 3. RegistrationObserver

Create `app/Observers/RegistrationObserver.php`.

Implement `deleted(Registration $registration)`:
- Only run if the deleted registration had `status === 'confirmed'`
- Find the oldest `waiting_list` registration for the same `event_categories_id` (order by created_at asc, take first)
- If one exists: update it to `status = 'confirmed'`
- Create a `Notification` record: `user_id` = promoted registration's user_id, `registration_id` = promoted registration's id, `type = 'confirm'`, `message = 'Selamat! Pendaftaran Anda telah dikonfirmasi karena ada peserta yang mengundurkan diri.'`

### 4. Register observer in AppServiceProvider

In `app/Providers/AppServiceProvider.php` boot():
```php
\App\Models\Registration::observe(\App\Observers\RegistrationObserver::class);
```

### 5. Shared layout — app.blade.php

Create `resources/views/layouts/app.blade.php`.

Simple full-page layout:
- `<head>`: title slot, vite assets, Alpine.js CDN
- `<nav>`: "Event Kampus" brand (links to /), nav links: "Events" → /events; if auth: "Notifikasi" → /notifications (show unread count badge if `auth()->user()->notifications()->whereNull('read_at')->count() > 0`), "Profile" → /profile, logout form button; if guest: "Login" → /login, "Daftar" → /register
- `<main>`: `{{ $slot }}`
- Flash messages: show `session('success')` in green div and `session('error')` in red div above slot

### 6. Admin layout — admin.blade.php

Create `resources/views/layouts/admin.blade.php`.

Simple two-column layout:
- Left sidebar: "Admin Panel" header, links: Dashboard → /admin, Kelola Event → /admin/events, Laporan → /admin/reports
- Right main area: `{{ $slot }}`
- Include vite assets and Alpine.js CDN in head

### 7. routes/web.php

Replace the entire `routes/web.php` with all routes for the app (placeholders for controllers that don't exist yet are fine — the routes just need to be defined so teammates can build on them):

```php
<?php

use Illuminate\Support\Facades\Route;

// Public
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');

// Auth
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showForm'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showForm'])->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->middleware('guest');

// Peserta (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/events/{event}/register', [\App\Http\Controllers\RegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/events/{event}/register', [\App\Http\Controllers\RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/registrations', [\App\Http\Controllers\RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    Route::get('events/{event}/participants', [\App\Http\Controllers\Admin\ParticipantController::class, 'index'])->name('events.participants');
    Route::post('events/{event}/participants/{registration}/checkin', [\App\Http\Controllers\Admin\ParticipantController::class, 'checkin'])->name('events.checkin');
    Route::delete('events/{event}/participants/{registration}', [\App\Http\Controllers\Admin\ParticipantController::class, 'destroy'])->name('events.participants.destroy');
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
});
```

## Deliverables

Files to create/modify:
- `app/Http/Middleware/EnsureAdmin.php` (create)
- `app/Observers/RegistrationObserver.php` (create)
- `app/Providers/AppServiceProvider.php` (modify — add observer registration)
- `bootstrap/app.php` (modify — add middleware alias)
- `resources/views/layouts/app.blade.php` (create)
- `resources/views/layouts/admin.blade.php` (create)
- `routes/web.php` (replace)

## Testing

Run `php artisan test` after finishing — all existing tests must still pass (7 tests).

If any test fails, fix the issue before committing.

## Commit

Single commit: `feat: layouts, EnsureAdmin middleware, RegistrationObserver, and routes`

## Report

Write your report to `.superpowers/sdd/task-1-report.md` with:
- Files created/modified
- Test result (pass/fail count)
- Any concerns

Return only: status (DONE or BLOCKED), one line summary, and the report path.
