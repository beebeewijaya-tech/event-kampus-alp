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
