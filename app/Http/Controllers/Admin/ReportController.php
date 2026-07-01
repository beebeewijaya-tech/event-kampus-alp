<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Registration;

class ReportController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $totalCategories = EventCategory::count();
        $totalRegistrations = Registration::count();
        $confirmedRegistrations = Registration::where('status', 'confirmed')->count();
        $waitingListRegistrations = Registration::where('status', 'waiting_list')->count();
        $checkedInToday = Registration::whereDate('checked_in_at', now()->toDateString())->count();

        $popularEvents = Event::withCount('registrations')
            ->orderByDesc('registrations_count')
            ->take(5)
            ->get();

        $latestRegistrations = Registration::with('eventCategory.event', 'user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.reports.index', compact(
            'totalEvents',
            'totalCategories',
            'totalRegistrations',
            'confirmedRegistrations',
            'waitingListRegistrations',
            'checkedInToday',
            'popularEvents',
            'latestRegistrations'
        ));
    }
}
