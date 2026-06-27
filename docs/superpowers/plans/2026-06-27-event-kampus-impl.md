# Pendaftaran Event Kampus — Implementation Plan

> **For agentic workers:** Use superpowers:executing-plans to implement task-by-task.

**Goal:** Build all 15 pages of the campus event registration app.  
**Architecture:** Laravel MVC, Blade + Tailwind 4 + Alpine.js (CDN), SQLite, custom notification table.  
**Tech:** PHP 8.3, Laravel 13, PHPUnit (in-memory SQLite for tests).  
**Run tests:** `php artisan test`  
**Dev server:** `composer run dev`

## Global Constraints
- Login required to register for events
- `registrations.status` = `pending | confirmed | waiting_list`
- `users` has a `phone` field (nullable)
- No Laravel built-in notifications — use custom `notifications` table
- Admin routes all under `/admin` guarded by `EnsureAdmin` middleware
- Poster images: `storage/app/public/posters`, served via `/storage/posters/`

---

## 🔴 Task 1 — Schema Migrations (Bee / Claude)
> **Hard — assigned to Claude**

**Files:**
- Create: `database/migrations/..._add_phone_to_users_table.php`
- Create: `database/migrations/..._add_waiting_list_to_registrations_status.php`

**Steps:**
- [ ] Create migration to add `phone` (string, nullable, after email) to `users`
- [ ] SQLite doesn't support ALTER COLUMN for enums — drop + recreate `status` column on `registrations` as string with DB-level default, validate in model/request
- [ ] Run `php artisan migrate` — confirm no errors
- [ ] Update `UserFactory` to include `phone`
- [ ] Write Feature test: register user with phone, assert stored; create registration with `waiting_list` status, assert stored
- [ ] Commit: `feat: add phone to users and waiting_list status to registrations`

---

## 🟡 Task 2 — Models & Relationships (Teammate A)

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Models/Event.php`, `EventCategory.php`, `Registration.php`, `Notification.php`

**Steps:**
- [ ] `User`: add `phone` to fillable; relationships: `hasMany Event`, `hasMany Registration`, `hasMany Notification`
- [ ] `Event`: fillable all fields; `belongsTo User`; `hasMany EventCategory`; scope `open()` filters `status=open AND registration_deadline > now`
- [ ] `EventCategory`: fillable; `belongsTo Event`; `hasMany Registration`; accessor `availableSlots()` = quota − confirmed count
- [ ] `Registration`: fillable + `status` cast; `belongsTo User`, `belongsTo EventCategory`; `check_in_code` auto-generated via `boot()` using `Str::upper(Str::random(8))`
- [ ] `Notification`: fillable; `belongsTo User`, `belongsTo Registration`
- [ ] Write Unit tests: `EventCategory::availableSlots()` returns correct count; `Registration` auto-generates `check_in_code` on create
- [ ] Commit: `feat: add models with relationships`

---

## 🟡 Task 3 — Auth: Login & Register (Teammate A)

**Files:**
- Create: `app/Http/Controllers/Auth/LoginController.php`, `RegisterController.php`
- Create: `resources/views/auth/login.blade.php`, `register.blade.php`
- Modify: `routes/web.php`

**Steps:**
- [ ] `RegisterController`: store name, email, phone, password; role defaults to `peserta`; redirect to `/events` after register
- [ ] `LoginController`: authenticate; redirect admin to `/admin`, peserta to `/events`
- [ ] Register form: name, email, phone, password, password_confirmation
- [ ] Login form: email, password
- [ ] Routes: `GET|POST /login`, `GET|POST /register`, `POST /logout`
- [ ] Feature test: user can register with phone → redirected; user can login → redirected by role; invalid credentials → error shown
- [ ] Commit: `feat: auth login and register with phone field`

---

## 🟡 Task 4 — Layouts & Middleware (Teammate B)

**Files:**
- Create: `app/Http/Middleware/EnsureAdmin.php`
- Create: `resources/views/layouts/app.blade.php`, `admin.blade.php`
- Modify: `bootstrap/app.php` (register middleware alias)

**Steps:**
- [ ] `EnsureAdmin`: if `auth()->user()->role !== 'admin'` → redirect `/` with error flash
- [ ] Register alias `admin` in `bootstrap/app.php` middleware
- [ ] `layouts/app.blade.php`: nav with logo, Events link, auth links, notification badge (unread count from `auth()->user()->notifications()->whereNull('read_at')->count()`), logout
- [ ] `layouts/admin.blade.php`: sidebar with Dashboard, Kelola Event, Laporan links; main content area
- [ ] Feature test: peserta cannot access `/admin` → redirected; admin can access `/admin`
- [ ] Commit: `feat: layouts and admin middleware`

---

## 🟡 Task 5 — Public Pages: Home, Event List, Detail (Teammate B)

**Files:**
- Create: `app/Http/Controllers/EventController.php`
- Create: `resources/views/home.blade.php`, `events/index.blade.php`, `events/show.blade.php`
- Modify: `routes/web.php`

**Steps:**
- [ ] `EventController@index`: paginate open events (10/page); pass to `events/index`
- [ ] `EventController@show`: load event with categories and their confirmed counts
- [ ] `home.blade.php`: show latest 6 open events as cards with poster, title, date, status badge
- [ ] `events/index.blade.php`: grid of event cards + simple search by title (`?q=`)
- [ ] `events/show.blade.php`: event detail (poster, title, desc, date, deadline), list of categories (name, price, quota, available slots), Register button (if logged in + event open) per category
- [ ] Routes: `GET /` → home, `GET /events` → index, `GET /events/{event}` → show
- [ ] Feature test: home loads with open events; show page displays categories with correct available slots
- [ ] Commit: `feat: public home, event list, and detail pages`

---

## 🔴 Task 6 — Registration Flow + Waiting List Logic (Bee / Claude)
> **Hard — assigned to Claude**

**Files:**
- Create: `app/Http/Controllers/RegistrationController.php`
- Create: `app/Http/Requests/StoreRegistrationRequest.php`
- Create: `app/Observers/RegistrationObserver.php`
- Create: `resources/views/registrations/create.blade.php`, `index.blade.php`
- Modify: `routes/web.php`, `app/Providers/AppServiceProvider.php`

**Steps:**
- [ ] `StoreRegistrationRequest`: validate `event_category_id` exists + belongs to the event
- [ ] `RegistrationController@create`: load event + categories; pass `auth()->user()` for auto-fill
- [ ] `RegistrationController@store`:
  - Check user hasn't already registered for this event (any category, any status)
  - Lock category row (`lockForUpdate`), count confirmed registrations
  - If count < quota → status = `confirmed`; else → status = `waiting_list`
  - Create registration (check_in_code auto via model boot)
  - Create notification (type=`confirm`, message differs by status)
  - Redirect to `/registrations` with success flash
- [ ] `RegistrationObserver@deleted` / `@updated` (status changed away from confirmed):
  - Find oldest `waiting_list` registration for the same `event_categories_id`
  - Promote to `confirmed`
  - Create notification for promoted user
- [ ] Register observer in `AppServiceProvider`
- [ ] `registrations/create.blade.php`: auto-filled name/email/phone (editable), category selector (radio buttons showing name, price, available slots, "Waiting List" label when full)
- [ ] `registrations/index.blade.php`: table of registrations with event name, category, status badge, check_in_code, checked_in_at
- [ ] Routes: `GET|POST /events/{event}/register`, `GET /registrations`
- [ ] Feature test:
  - Register when quota available → `confirmed` + notification created
  - Register when quota full → `waiting_list` + notification with waiting list message
  - Delete confirmed registration → observer promotes first waiting_list to confirmed + creates notification
  - User cannot register twice for same event
- [ ] Commit: `feat: registration flow with quota check and waiting list promotion`

---

## 🟡 Task 7 — Profile & Notifications (Teammate A)

**Files:**
- Create: `app/Http/Controllers/ProfileController.php`, `NotificationController.php`
- Create: `resources/views/profile/edit.blade.php`, `notifications/index.blade.php`
- Modify: `routes/web.php`

**Steps:**
- [ ] `ProfileController@edit` / `@update`: update name, email, phone; validate email unique except current user
- [ ] `NotificationController@index`: paginate user's notifications newest-first; eager load `registration.eventCategory.event`
- [ ] `NotificationController@read`: set `read_at = now()` on a notification belonging to auth user
- [ ] `profile/edit.blade.php`: form with name, email, phone fields; success flash
- [ ] `notifications/index.blade.php`: list with type badge, message, event name, timestamp, "mark read" button; unread items highlighted
- [ ] Routes (auth): `GET|PUT /profile`, `GET /notifications`, `POST /notifications/{notification}/read`
- [ ] Feature test: user can update phone; notification read_at set on mark-read; cannot mark another user's notification
- [ ] Commit: `feat: profile edit and notifications pages`

---

## 🔴 Task 8 — Admin: Event CRUD + Categories (Bee / Claude)
> **Hard — assigned to Claude**

**Files:**
- Create: `app/Http/Controllers/Admin/EventController.php`
- Create: `app/Http/Requests/StoreEventRequest.php`
- Create: `resources/views/admin/events/index.blade.php`, `create.blade.php`, `edit.blade.php`
- Modify: `routes/web.php`

**Steps:**
- [ ] `StoreEventRequest`: validate title, description, event_date, registration_deadline, status; poster_img as image file optional on update
- [ ] `Admin\EventController`: full CRUD; on store/update handle poster upload to `storage/app/public/posters`; on delete remove poster file
- [ ] `create.blade.php` / `edit.blade.php`: event fields + dynamic category rows (Alpine.js `x-for`): add/remove category rows with name, quota, price, description. Submit categories as `categories[]` array.
- [ ] `index.blade.php`: table of events with title, date, status toggle button, edit/delete links; pagination
- [ ] `Admin\EventController@storeCategories`: sync categories (delete old + insert new) when event saved
- [ ] Routes (admin): `GET|POST /admin/events`, `GET /admin/events/create`, `GET|PUT|DELETE /admin/events/{event}`, `GET /admin/events/{event}/edit`
- [ ] Feature test: admin can create event with 2 categories; edit changes category quota; delete removes event + poster file; peserta cannot access
- [ ] Commit: `feat: admin event CRUD with dynamic category management`

---

## 🔴 Task 9 — Admin: Participants, Check-in, Dashboard (Bee / Claude)
> **Hard — assigned to Claude**

**Files:**
- Create: `app/Http/Controllers/Admin/ParticipantController.php`, `DashboardController.php`
- Create: `resources/views/admin/participants/index.blade.php`, `dashboard.blade.php`
- Modify: `routes/web.php`

**Steps:**
- [ ] `DashboardController@index`: pass counts — total events, total registrations, today's check-ins (`checked_in_at::date = today`), total confirmed registrations
- [ ] `dashboard.blade.php`: 4 stat cards; recent 5 events table
- [ ] `ParticipantController@index`: load event; confirmed registrations + waiting_list registrations separately; pass both to view
- [ ] `participants/index.blade.php`: two tabs (Alpine.js) — "Peserta" (confirmed, with check-in status) and "Waiting List"; each row has name, email, category, check_in_code, checked_in_at; check-in button; remove button (with confirm dialog)
- [ ] `ParticipantController@checkin`: set `checked_in_at = now()` on registration; validate registration belongs to this event
- [ ] `ParticipantController@destroy`: delete registration → observer fires waiting list promotion
- [ ] Routes (admin): `GET /admin`, `GET /admin/events/{event}/participants`, `POST /admin/events/{event}/participants/{registration}/checkin`, `DELETE /admin/events/{event}/participants/{registration}`
- [ ] Feature test: check-in sets checked_in_at; destroy promotes waiting list; dashboard counts correct
- [ ] Commit: `feat: admin dashboard, participant list, and check-in`

---

## 🟡 Task 10 — Admin: Reports (Teammate B)

**Files:**
- Create: `app/Http/Controllers/Admin/ReportController.php`
- Create: `resources/views/admin/reports/index.blade.php`
- Modify: `routes/web.php`

**Steps:**
- [ ] `ReportController@index`: per-event stats — total registrations, confirmed count, waiting_list count, check-in count, revenue (sum price of confirmed); return as collection
- [ ] `reports/index.blade.php`: table per event; progress bar for check-in rate (checked_in / confirmed × 100); totals row at bottom
- [ ] Route (admin): `GET /admin/reports`
- [ ] Feature test: revenue = sum of category prices for confirmed registrations only
- [ ] Commit: `feat: admin reports and statistics`

---

## Task Assignment Summary

| Task | Who | Difficulty |
|------|-----|------------|
| 1 — Schema migrations | Claude | 🔴 Hard |
| 2 — Models | Teammate A | 🟡 Medium |
| 3 — Auth | Teammate A | 🟡 Medium |
| 4 — Layouts & Middleware | Teammate B | 🟡 Medium |
| 5 — Public pages | Teammate B | 🟡 Medium |
| 6 — Registration + Waiting List | Claude | 🔴 Hard |
| 7 — Profile & Notifications | Teammate A | 🟡 Medium |
| 8 — Admin Event CRUD | Claude | 🔴 Hard |
| 9 — Admin Participants + Dashboard | Claude | 🔴 Hard |
| 10 — Admin Reports | Teammate B | 🟡 Medium |

**Suggested order:** 1 → 2 → 3+4 (parallel) → 5 → 6 → 7+8 (parallel) → 9 → 10
