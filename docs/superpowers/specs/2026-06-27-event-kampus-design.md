# Pendaftaran Event Kampus — Design Spec
Date: 2026-06-27
Team: Suradi, Bee Bee Wijaya, Sitti Andina Nurafifah, Jonathan Lumbanbatu

---

## 1. Overview

Web application for campus event registration. Two roles: **Admin** (creates/manages events and participants) and **Peserta** (registers for events, checks in, receives notifications).

**Stack:** Laravel 13, PHP 8.3, SQLite, Tailwind CSS 4, Alpine.js (via CDN), Vite.

---

## 2. Database Schema (Final)

### users
| Key | Field | Type |
|-----|-------|------|
| PK | id | BIGINT auto-increment |
| | name | VARCHAR |
| | email | VARCHAR unique |
| | phone | VARCHAR nullable |
| | password | VARCHAR (bcrypt) |
| | role | ENUM('admin','peserta') default peserta |
| | timestamps | created_at, updated_at |

**Change from original:** Add `phone` column (missing from original schema and ERD).

### events
| Key | Field | Type |
|-----|-------|------|
| PK | id | BIGINT |
| FK | user_id | → users.id (admin creator) |
| | title | VARCHAR |
| | description | TEXT |
| | poster_img | VARCHAR (file path) |
| | event_date | TIMESTAMP |
| | registration_deadline | TIMESTAMP |
| | status | ENUM('open','closed') |
| | timestamps | |

### event_categories
| Key | Field | Type |
|-----|-------|------|
| PK | id | BIGINT |
| FK | event_id | → events.id |
| | name | VARCHAR (e.g. VIP, Regular, Online) |
| | quota | INT unsigned |
| | price | DECIMAL(10,2) default 0 |
| | description | TEXT nullable |
| | timestamps | |

### registrations
| Key | Field | Type |
|-----|-------|------|
| PK | id | BIGINT |
| FK | user_id | → users.id |
| FK | event_categories_id | → event_categories.id |
| UQ | check_in_code | VARCHAR unique |
| | checked_in_at | TIMESTAMP nullable |
| | status | ENUM('pending','confirmed','waiting_list') |
| | timestamps | |

**Change from original:** Add `waiting_list` to status enum.

### notifications
| Key | Field | Type |
|-----|-------|------|
| PK | id | BIGINT |
| FK | user_id | → users.id |
| FK | registration_id | → registrations.id |
| | type | ENUM('confirm','reminder') |
| | message | TEXT |
| | status | ENUM('pending','confirmed') default pending |
| | read_at | TIMESTAMP nullable |
| | timestamps | |

---

## 3. Waiting List Mechanism

1. On registration submission, system counts `confirmed` registrations for the chosen category.
2. If `confirmed_count < quota` → status = `confirmed`.
3. If `confirmed_count >= quota` → status = `waiting_list`.
4. A `confirm`-type notification is created in both cases (message differs: "Pendaftaran dikonfirmasi" vs "Kamu masuk waiting list").
5. When admin removes a `confirmed` participant (or participant cancels), a `RegistrationObserver` fires on status change/delete:
   - Queries oldest `waiting_list` registration for that category.
   - Promotes it to `confirmed`.
   - Creates a `confirm` notification: "Kamu telah dipindahkan dari waiting list ke peserta terkonfirmasi".

---

## 4. Pages & Routes

### Public (no auth required)
| Route | Page |
|-------|------|
| `GET /` | Home — featured events, search |
| `GET /events` | Daftar Event — browse all open events, filter by category name |
| `GET /events/{id}` | Detail Event — event info, category list with quota/price/availability |

### Auth
| Route | Page |
|-------|------|
| `GET /login` | Login form |
| `POST /login` | Authenticate |
| `GET /register` | Register form (name, email, phone, password) |
| `POST /register` | Create account |
| `POST /logout` | Logout |

### Peserta (auth + role:peserta)
| Route | Page |
|-------|------|
| `GET /events/{id}/register` | Pendaftaran Event — category selector, auto-filled name/email/phone |
| `POST /events/{id}/register` | Submit registration |
| `GET /registrations` | Riwayat Pendaftaran — list with statuses (pending, confirmed, waiting_list, checked_in) |
| `GET /profile` | Profile User |
| `PUT /profile` | Update name, email, phone |
| `GET /notifications` | Notifikasi — list, mark read |
| `POST /notifications/{id}/read` | Mark single notification read |

### Admin (auth + role:admin)
| Route | Page |
|-------|------|
| `GET /admin` | Dashboard — total events, registrations, today check-ins, revenue |
| `GET /admin/events` | Kelola Event — paginated list with status toggle |
| `GET /admin/events/create` | Form Tambah Event |
| `POST /admin/events` | Store event |
| `GET /admin/events/{id}/edit` | Form Edit Event |
| `PUT /admin/events/{id}` | Update event |
| `DELETE /admin/events/{id}` | Delete event |
| `GET /admin/events/{id}/participants` | Daftar Peserta — confirmed list + waiting list tab |
| `POST /admin/events/{id}/participants/{reg}/checkin` | Check-in peserta |
| `DELETE /admin/events/{id}/participants/{reg}` | Remove participant (triggers waiting list promotion) |
| `GET /admin/reports` | Laporan / Statistik — charts per event, export |

---

## 5. Architecture

```
app/
  Http/
    Controllers/
      Auth/
        LoginController.php
        RegisterController.php
      EventController.php          — public browse + detail
      RegistrationController.php   — register, history
      ProfileController.php
      NotificationController.php
      Admin/
        DashboardController.php
        EventController.php        — CRUD + categories
        ParticipantController.php  — list, check-in, remove
        ReportController.php
    Middleware/
      EnsureAdmin.php
    Requests/
      StoreEventRequest.php
      StoreRegistrationRequest.php
      UpdateProfileRequest.php
  Models/
    User.php
    Event.php
    EventCategory.php
    Registration.php
    Notification.php
  Observers/
    RegistrationObserver.php       — waiting list promotion + notification
  Policies/
    RegistrationPolicy.php

resources/views/
  layouts/
    app.blade.php                  — peserta layout (nav with notif badge)
    admin.blade.php                — admin layout (sidebar)
  auth/
    login.blade.php
    register.blade.php
  events/
    index.blade.php
    show.blade.php
  registrations/
    create.blade.php               — category selector + auto-fill
    index.blade.php                — history
  profile/
    edit.blade.php
  notifications/
    index.blade.php
  home.blade.php
  admin/
    dashboard.blade.php
    events/
      index.blade.php
      create.blade.php
      edit.blade.php
    participants/
      index.blade.php              — confirmed + waiting list tabs
      checkin.blade.php
    reports/
      index.blade.php
```

---

## 6. Key Implementation Notes

**Registration auto-fill:** `GET /events/{id}/register` passes `auth()->user()` to the view; Blade pre-populates name, email, phone fields (still editable).

**Category selector:** On the registration page, show a radio/select of the event's categories with name, price, quota, and available slots. Available slots = `quota - confirmed_count`. Disable categories with 0 available slots (still registerable as waiting_list — show "Waiting List" label instead of disabling).

**check_in_code generation:** `Str::upper(Str::random(8))` on registration creation, retried on collision (extremely rare with unique constraint).

**Poster upload:** Store in `storage/app/public/posters`, symlink via `php artisan storage:link`. Reference as `/storage/posters/filename`.

**Notification status field:** The `status` field on notifications tracks delivery status (pending = not yet sent/shown, confirmed = delivered/shown). `read_at` tracks whether the user has read it. Unread count shown in nav badge.

**Admin middleware:** `EnsureAdmin` checks `auth()->user()->role === 'admin'`, redirects peserta to `/` with an error flash.

**Laporan / Statistik:** Aggregates via Eloquent — registrations per event, confirmed vs waiting_list ratio, check-in rate, revenue (sum of category prices for confirmed registrations). No charting library needed — plain HTML tables with progress bars via Tailwind.

---

## 7. Migrations Required

1. `add_phone_to_users_table` — `$table->string('phone')->nullable()->after('email')`
2. `update_registrations_status_enum` — SQLite doesn't support ALTER COLUMN for enums; recreate the column via a raw statement or use a new migration that handles SQLite's limitations (drop+recreate column or use a string column instead of enum, or handle via a fresh migration sequence).

> **SQLite note:** SQLite does not enforce ENUMs natively and does not support `ALTER COLUMN`. The enum is stored as TEXT with application-level validation. To add `waiting_list`, a new migration can simply drop and recreate the column (SQLite allows this via table rebuild), or since the DB is in early dev, a fresh migrate:fresh is acceptable.

---

## 8. Feedback Resolutions

| Feedback Point | Resolution |
|---|---|
| Login required to register? | Yes, login required. Registration form auto-fills from user profile. |
| No phone field on users | Add via new migration + update register form + profile page |
| No category selection on registration | Category selector on registration page; FK already in registrations table |
| Waiting list in UI but no DB mechanism | Add `waiting_list` to enum + Observer-driven promotion logic |
| Notification system works? | Custom table-based, not Laravel's built-in. Triggered by Observer on registration status change. |
