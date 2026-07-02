<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('registrations')->latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'poster_img' => ['required', 'string', 'max:255'],
                'event_date' => ['required', 'date'],
                'registration_deadline' => ['required', 'date', 'before_or_equal:event_date'],
                'status' => ['required', 'in:open,closed'],

                'categories' => ['required', 'array', 'min:1'],
                'categories.*.name' => ['required', 'string', 'max:255'],
                'categories.*.quota' => ['required', 'integer', 'min:1'],
                'categories.*.price' => ['required', 'numeric', 'min:0'],
                'categories.*.description' => ['nullable', 'string'],
            ],
            [
                'categories.required' => 'Minimal harus ada satu kategori tiket.',
                'categories.min' => 'Minimal harus ada satu kategori tiket.',
                'categories.*.name.required' => 'Nama kategori wajib diisi.',
                'categories.*.quota.required' => 'Kuota wajib diisi.',
                'categories.*.price.required' => 'Harga wajib diisi.',
            ]
        );

        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $data['user_id'] = Auth::id();

        $event = Event::create($data);

        foreach ($categories as $category) {
            $event->categories()->create($category);
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function show(Event $event)
    {
        $event->load('categories');
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $event->load('categories');
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'poster_img' => ['required', 'string', 'max:255'],
                'event_date' => ['required', 'date'],
                'registration_deadline' => ['required', 'date', 'before_or_equal:event_date'],
                'status' => ['required', 'in:open,closed'],

                'categories' => ['required', 'array', 'min:1'],
                'categories.*.id' => ['nullable', 'exists:event_categories,id'],
                'categories.*.name' => ['required', 'string', 'max:255'],
                'categories.*.quota' => ['required', 'integer', 'min:1'],
                'categories.*.price' => ['required', 'numeric', 'min:0'],
                'categories.*.description' => ['nullable', 'string'],
            ],
            [
                'categories.required' => 'Minimal harus ada satu kategori tiket.',
                'categories.min' => 'Minimal harus ada satu kategori tiket.',
                'categories.*.name.required' => 'Nama kategori wajib diisi.',
                'categories.*.quota.required' => 'Kuota wajib diisi.',
                'categories.*.price.required' => 'Harga wajib diisi.',
            ]
        );

        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $event->update($data);

        $categoryIds = [];

        foreach ($categories as $category) {
            $categoryId = $category['id'] ?? null;
            unset($category['id']);

            if ($categoryId) {
                $eventCategory = $event->categories()->where('id', $categoryId)->first();

                if ($eventCategory) {
                    $eventCategory->update($category);
                    $categoryIds[] = $eventCategory->id;
                }
            } else {
                $newCategory = $event->categories()->create($category);
                $categoryIds[] = $newCategory->id;
            }
        }

        $event->categories()
            ->whereNotIn('id', $categoryIds)
            ->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}
