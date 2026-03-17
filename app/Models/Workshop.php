<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'start_time',
        'end_time',
        'capacity',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function confirmedRegistrations()
    {
        return $this->hasMany(Registration::class)->where('status', 'confirmed');
    }

    public function waitingRegistrations()
    {
        return $this->hasMany(Registration::class)
            ->where('status', 'waiting')
            ->orderBy('waiting_position');
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->confirmedRegistrations()->count());
    }

    public function isFull(): bool
    {
        return $this->confirmedRegistrations()->count() >= $this->capacity;
    }
}
