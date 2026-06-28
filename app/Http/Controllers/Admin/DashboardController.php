<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $totalRegistrations = Registration::count();
        $confirmedRegistrations = Registration::where('status', 'confirmed')->count();
        $recentEvents = Event::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalEvents', 'totalRegistrations', 'confirmedRegistrations', 'recentEvents'));
    }
}
