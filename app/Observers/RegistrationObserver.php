<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Registration;

class RegistrationObserver
{
    public function deleted(Registration $registration): void
    {
        if ($registration->status !== 'confirmed') {
            return;
        }

        $next = Registration::where('event_categories_id', $registration->event_categories_id)
            ->where('status', 'waiting_list')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($next) {
            $next->update(['status' => 'confirmed']);

            Notification::create([
                'user_id'         => $next->user_id,
                'registration_id' => $next->id,
                'type'            => 'confirm',
                'message'         => 'Selamat! Pendaftaran Anda telah dikonfirmasi karena ada peserta yang mengundurkan diri.',
            ]);
        }
    }
}
