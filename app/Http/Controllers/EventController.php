<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::open()->latest()->paginate(12);

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('categories');

        foreach ($event->categories as $category) {
            $category->confirmed_count = $category->registrations()->where('status', 'confirmed')->count();
        }

        return view('events.show', compact('event'));
    }
}
