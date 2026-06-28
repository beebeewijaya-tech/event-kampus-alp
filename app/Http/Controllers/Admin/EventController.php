<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store()
    {
        // TODO: Suradi
        return back()->with('error', 'Belum diimplementasi.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Event $event)
    {
        // TODO: Suradi
        return back()->with('error', 'Belum diimplementasi.');
    }

    public function destroy(Event $event)
    {
        // TODO: Suradi
        return back()->with('error', 'Belum diimplementasi.');
    }
}
