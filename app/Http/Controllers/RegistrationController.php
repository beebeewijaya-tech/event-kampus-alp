<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Notification;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function create(Event $event)
    {
        $event->load('categories');
        $selectedCategoryId = request('category');
        $user = auth()->user();

        return view('registrations.create', compact('event', 'selectedCategoryId', 'user'));
    }

    public function store(Event $event, StoreRegistrationRequest $request)
    {
        $sudahDaftar = Registration::whereHas('eventCategory', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })->where('user_id', auth()->id())->exists();

        if ($sudahDaftar) {
            return back()->with('error', 'Anda sudah mendaftar untuk event ini.');
        }

        $category = EventCategory::findOrFail($request->event_category_id);

        $jumlahKonfirmasi = Registration::where('event_categories_id', $category->id)
            ->where('status', 'confirmed')
            ->count();

        $status = $jumlahKonfirmasi < $category->quota ? 'confirmed' : 'waiting_list';

        $reg = Registration::create([
            'user_id' => auth()->id(),
            'event_categories_id' => $category->id,
            'status' => $status,
        ]);

        if ($status === 'confirmed') {
            $pesan = 'Pendaftaran Anda untuk ' . $event->title . ' telah dikonfirmasi.';
        } else {
            $pesan = 'Anda masuk dalam daftar tunggu untuk ' . $event->title . '.';
        }

        Notification::create([
            'user_id' => auth()->id(),
            'registration_id' => $reg->id,
            'type' => 'confirm',
            'message' => $pesan,
        ]);

        return redirect()->route('registrations.index')->with('success', 'Pendaftaran berhasil!');
    }

    public function index()
    {
        $registrations = auth()->user()->registrations()->with('eventCategory.event')->latest()->get();

        return view('registrations.index', compact('registrations'));
    }
}
