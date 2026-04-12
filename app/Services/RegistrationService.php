<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    /**
     * Register a user for a workshop.
     *
     * Returns null on success, or an error string if registration is not allowed.
     */
    public function register(User $user, Workshop $workshop): ?string
    {
        if ($workshop->start_time->isPast()) {
            return 'This workshop has already started.';
        }

        return DB::transaction(function () use ($user, $workshop) {
            Workshop::lockForUpdate()->findOrFail($workshop->id);

            if ($workshop->registrations()->where('user_id', $user->id)->exists()) {
                return 'You are already registered for this workshop.';
            }

            $overlap = Registration::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->whereHas('workshop', function ($q) use ($workshop) {
                    $q->where('start_time', '<', $workshop->end_time)
                      ->where('end_time', '>', $workshop->start_time);
                })
                ->exists();

            if ($overlap) {
                return 'You already have a confirmed workshop that overlaps with this one.';
            }

            if ($workshop->isFull()) {
                $nextPosition = $workshop->waitingRegistrations()->max('waiting_position') + 1;
                Registration::create([
                    'workshop_id' => $workshop->id,
                    'user_id'     => $user->id,
                    'status'      => 'waiting',
                    'waiting_position' => $nextPosition,
                ]);
            } else {
                Registration::create([
                    'workshop_id' => $workshop->id,
                    'user_id'     => $user->id,
                    'status'      => 'confirmed',
                ]);
            }

            return null;
        });
    }

    /**
     * Cancel a user's registration and promote the first eligible waiting user.
     */
    public function cancel(Registration $registration): void
    {
        $workshop = $registration->workshop;

        DB::transaction(function () use ($registration, $workshop) {
            $wasConfirmed = $registration->status === 'confirmed';
            $registration->delete();

            if ($wasConfirmed) {
                $this->promoteNextEligible($workshop);
            }

            $this->renumberWaitingList($workshop);
        });
    }

    private function promoteNextEligible(Workshop $workshop): void
    {
        foreach ($workshop->waitingRegistrations()->get() as $waiting) {
            $hasOverlap = Registration::where('user_id', $waiting->user_id)
                ->where('status', 'confirmed')
                ->whereHas('workshop', function ($q) use ($workshop) {
                    $q->where('start_time', '<', $workshop->end_time)
                      ->where('end_time', '>', $workshop->start_time);
                })
                ->exists();

            if (! $hasOverlap) {
                $waiting->update([
                    'status'           => 'confirmed',
                    'waiting_position' => null,
                ]);
                return;
            }
        }
    }

    private function renumberWaitingList(Workshop $workshop): void
    {
        $workshop->waitingRegistrations()
            ->orderBy('waiting_position')
            ->get()
            ->each(fn ($reg, $index) => $reg->update(['waiting_position' => $index + 1]));
    }
}
