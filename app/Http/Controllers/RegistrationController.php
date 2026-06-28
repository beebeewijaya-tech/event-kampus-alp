<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Notification;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function create(Event $event)
    {
        $event->load('categories');
        $selectedCategoryId = request('category');
        $categories = $event->categories;
        $user = auth()->user();

        return view('registrations.create', compact('event', 'categories', 'selectedCategoryId', 'user'));
    }

    public function store(Event $event, StoreRegistrationRequest $request)
    {
        $alreadyRegistered = Registration::whereHas('eventCategory', fn($q) => $q->where('event_id', $event->id))
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'Anda sudah mendaftar untuk event ini.');
        }

        $category = EventCategory::findOrFail($request->event_category_id);

        DB::transaction(function () use ($event, $category) {
            $confirmedCount = Registration::where('event_categories_id', $category->id)
                ->where('status', 'confirmed')
                ->lockForUpdate()
                ->count();

            $status = $confirmedCount < $category->quota ? 'confirmed' : 'waiting_list';

            $registration = Registration::create([
                'user_id'             => auth()->id(),
                'event_categories_id' => $category->id,
                'status'              => $status,
            ]);

            $message = $status === 'confirmed'
                ? 'Pendaftaran Anda untuk ' . $event->title . ' telah dikonfirmasi.'
                : 'Anda masuk dalam daftar tunggu untuk ' . $event->title . '.';

            Notification::create([
                'user_id'         => auth()->id(),
                'registration_id' => $registration->id,
                'type'            => 'confirm',
                'message'         => $message,
            ]);
        });

        return redirect()->route('registrations.index')->with('success', 'Pendaftaran berhasil!');
    }

    public function index()
    {
        $registrations = auth()->user()->registrations()->with('eventCategory.event')->latest()->get();

        return view('registrations.index', compact('registrations'));
    }
}
