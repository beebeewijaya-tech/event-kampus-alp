# Task 4 Report — Notifikasi Page

## Status: DONE

## Summary

Implemented the notifications page: controller with `index` and `markRead` actions, and a Blade view listing notifications per user with per-item mark-read functionality.

## Files Created

- `app/Http/Controllers/NotificationController.php`
- `resources/views/notifications/index.blade.php`

## What Was Done

1. **NotificationController** — `index()` fetches the authenticated user's notifications ordered by latest and returns the view. `markRead()` authorizes ownership via `abort_if`, sets `read_at = now()`, and redirects back.

2. **notifications/index.blade.php** — Uses `<x-layouts.app>`. Lists notifications with unread items highlighted in `bg-blue-50` and read items in `bg-white`. Each item shows a type badge (green "Konfirmasi" for `confirm`, blue "Pengingat" for `reminder`), the message, a relative timestamp via `diffForHumans()`, and a "Tandai Dibaca" POST form button for unread items. Shows "Tidak ada notifikasi." when empty.

## Tests

All 9 existing tests passed (`php artisan test`).

## Commit

`feat: notifications page` — commit `6345b5c`
