<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index(Event $event)
    {
        $event->load('categories.registrations');

        return view('admin.event-categories.index', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'quota' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $event->categories()->create($data);

        return redirect()
            ->route('admin.events.categories.index', $event)
            ->with('success', 'Kategori event berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event, EventCategory $category)
    {
        abort_if($category->event_id !== $event->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'quota' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect()
            ->route('admin.events.categories.index', $event)
            ->with('success', 'Kategori event berhasil diperbarui.');
    }

    public function destroy(Event $event, EventCategory $category)
    {
        abort_if($category->event_id !== $event->id, 404);

        if ($category->registrations()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena sudah memiliki peserta.');
        }

        $category->delete();

        return redirect()
            ->route('admin.events.categories.index', $event)
            ->with('success', 'Kategori event berhasil dihapus.');
    }
}
