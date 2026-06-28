# Task 1 Report — Foundation: Layout, Middleware, Observer, Routes

## Files Created

- `app/Http/Middleware/EnsureAdmin.php` — redirects unauthenticated users to /login, non-admin users to / with error flash
- `app/Observers/RegistrationObserver.php` — on confirmed registration deletion, promotes oldest waiting_list entry and creates confirm notification
- `resources/views/layouts/app.blade.php` — main layout with nav, flash messages, Vite assets, Alpine.js CDN
- `resources/views/layouts/admin.blade.php` — two-column admin layout with sidebar links

## Files Modified

- `app/Providers/AppServiceProvider.php` — registered RegistrationObserver in boot()
- `bootstrap/app.php` — added `admin` middleware alias for EnsureAdmin
- `routes/web.php` — replaced with full route definitions (public, auth, peserta, admin)

## Test Result

9 tests passed, 0 failed (11 assertions, 138ms). All pre-existing tests continue to pass.

## Concerns

None. Routes reference controllers that don't exist yet (teammates will build them). Laravel resolves route definitions lazily so undefined controller classes do not cause test failures.
