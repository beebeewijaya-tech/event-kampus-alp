<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'quota', 'price', 'description',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'event_categories_id');
    }

    public function availableSlots(): int
    {
        return max(0, $this->quota - $this->registrations()->where('status', 'confirmed')->count());
    }

    public function isFull(): bool
    {
        return $this->availableSlots() === 0;
    }
}
