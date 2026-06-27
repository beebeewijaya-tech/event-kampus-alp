<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_categories_id', 'check_in_code',
        'checked_in_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Registration $registration) {
            if (empty($registration->check_in_code)) {
                do {
                    $code = Str::upper(Str::random(8));
                } while (static::where('check_in_code', $code)->exists());

                $registration->check_in_code = $code;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_categories_id');
    }

    public function notification(): HasOne
    {
        return $this->hasOne(Notification::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }
}
