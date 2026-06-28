<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;

class ParticipantController extends Controller
{
    public function index(Event $event)
    {
        $confirmed = Registration::whereHas('eventCategory', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })->where('status', 'confirmed')->with('user', 'eventCategory')->get();

        $waitingList = Registration::whereHas('eventCategory', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })->where('status', 'waiting_list')->with('user', 'eventCategory')->get();

        return view('admin.participants.index', compact('event', 'confirmed', 'waitingList'));
    }

    public function checkin(Event $event, Registration $registration)
    {
        if ($registration->eventCategory->event_id !== $event->id) {
            abort(403);
        }

        $registration->update(['checked_in_at' => now()]);

        return back()->with('success', 'Check-in berhasil.');
    }

    public function destroy(Event $event, Registration $registration)
    {
        if ($registration->eventCategory->event_id !== $event->id) {
            abort(403);
        }

        $registration->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
