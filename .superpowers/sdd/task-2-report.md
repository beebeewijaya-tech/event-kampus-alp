# Task 2 Report — Daftar Event + Detail Event

## Status: DONE

## What was built

### Files created

- `app/Http/Controllers/EventController.php` — two methods: `index()` paginates open events (12 per page), `show(Event $event)` eager-loads categories and attaches confirmed_count to each.
- `resources/views/events/index.blade.php` — responsive 3-column card grid using `<x-layouts.app>`. Each card shows poster (or gray placeholder), title, formatted event date, open/closed badge, and "Lihat Detail" link. Pagination via `$events->links()`.
- `resources/views/events/show.blade.php` — full event detail page: poster, title, status badge, description, event date, registration deadline, and a categories table (Name, Quota, Available Slots, Price, Action). Action column handles: open + authenticated → Daftar/Daftar Waiting List button; open + guest → Login untuk Daftar; closed → Pendaftaran Ditutup text.

## Key decisions

- `confirmed_count` attached per category in the controller loop as specified in the brief; `isFull` / `availableSlots` derived in the Blade template with `@php`.
- `Storage::url()` used for poster images; the facade import was added to the controller but Blade templates call it directly without needing an import.
- Tailwind classes kept simple and minimal — no complex components.

## Tests

All 9 tests passed (`php artisan test`).

## Commit

`feat: event list and detail pages` (commit 9f37722)
