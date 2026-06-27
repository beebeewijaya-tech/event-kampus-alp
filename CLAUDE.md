# Pendaftaran Event Kampus

Campus event registration web app. Students register for events; admins manage events and participants.

## Stack

- **Framework:** Laravel 13, PHP 8.3
- **Database:** SQLite (`database/database.sqlite`)
- **Frontend:** Blade templates, Tailwind CSS 4 (via `@tailwindcss/vite`), Alpine.js (CDN)
- **Assets:** Vite
- **Queue:** database driver
- **Mail:** log driver (dev)

## Dev Commands

```bash
composer run dev      # starts server + queue + logs + vite concurrently
php artisan serve     # server only
npm run dev           # vite only
php artisan migrate
php artisan migrate:fresh --seed
php artisan storage:link   # needed for poster image uploads
```

## Roles

- `admin` — creates and manages events, categories, participants, check-in, reports
- `peserta` — browses events, registers (with category selection), views history, checks notifications

## Database Tables

| Table | Purpose |
|-------|---------|
| `users` | All users; role = admin\|peserta; has phone field |
| `events` | Events created by admins |
| `event_categories` | Categories per event (VIP/Regular/etc) with quota and price |
| `registrations` | Registrations; status = pending\|confirmed\|waiting_list |
| `notifications` | App notifications; type = confirm\|reminder |

## Key Rules

- Registration requires login. Form auto-fills name/email/phone from user profile.
- On registration: if `confirmed_count >= quota` for chosen category → status = `waiting_list`.
- When a confirmed participant is removed: `RegistrationObserver` promotes the oldest waiting_list entry for that category to `confirmed` and creates a notification.
- `check_in_code` is generated as `Str::upper(Str::random(8))` on registration creation.
- Poster images stored in `storage/app/public/posters`, served via `/storage/posters/`.
- `EnsureAdmin` middleware guards all `/admin/*` routes.

## Pages (15 total)

**Public:** Home, Daftar Event, Detail Event  
**Auth:** Login, Register  
**Peserta:** Pendaftaran Event, Riwayat Pendaftaran, Profile User, Notifikasi  
**Admin:** Dashboard, Kelola Event, Form Tambah/Edit Event, Daftar Peserta, Check-in Peserta, Laporan/Statistik

## Design Spec

Full spec: `docs/superpowers/specs/2026-06-27-event-kampus-design.md`  
ERD source: `docs/raw/ERD-event-kampus.drawio`  
Project doc: `docs/raw/Web Development - AFL 3 - Kelompok 4.docx`

## SQLite Notes

SQLite does not enforce ENUMs (stored as TEXT) and does not support `ALTER COLUMN`. Enum additions require a table rebuild migration or `migrate:fresh` in early dev.
