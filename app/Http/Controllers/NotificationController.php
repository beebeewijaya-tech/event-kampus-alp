<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update(['read_at' => now()]);

        return back();
    }
}
