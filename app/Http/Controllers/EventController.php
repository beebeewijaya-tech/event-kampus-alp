<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $categories = EventCategory::select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $events = Event::open()
            ->with(['categories.registrations'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhereHas('categories', function ($cat) use ($search) {
                            $cat->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($category, function ($query, $category) {
                $query->whereHas('categories', function ($cat) use ($category) {
                    $cat->where('name', $category);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('events.index', compact('events', 'categories', 'search', 'category'));
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
