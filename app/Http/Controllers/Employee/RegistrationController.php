<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $workshops = Workshop::where('start_time', '>', now())
            ->withCount('confirmedRegistrations')
            ->orderBy('start_time')
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'title' => $w->title,
                'description' => $w->description,
                'start_time' => $w->start_time->toISOString(),
                'end_time' => $w->end_time->toISOString(),
                'capacity' => $w->capacity,
                'available_seats' => max(0, $w->capacity - $w->confirmed_registrations_count),
                'registration' => $w->registrations()->where('user_id', $user->id)->first()?->only(['id', 'status', 'waiting_position']),
            ]);

        return Inertia::render('Employee/Dashboard', [
            'workshops' => $workshops,
        ]);
    }

    public function store(Request $request, Workshop $workshop)
    {
        $user = $request->user();

        // Already registered?
        if ($workshop->registrations()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'You are already registered for this workshop.');
        }

        // No-ubiquity check: overlapping confirmed registrations
        $overlap = Registration::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereHas('workshop', function ($q) use ($workshop) {
                $q->where('start_time', '<', $workshop->end_time)
                  ->where('end_time', '>', $workshop->start_time);
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'You already have a confirmed workshop that overlaps with this one.');
        }

        DB::transaction(function () use ($workshop, $user) {
            if ($workshop->isFull()) {
                $nextPosition = $workshop->waitingRegistrations()->max('waiting_position') + 1;
                Registration::create([
                    'workshop_id' => $workshop->id,
                    'user_id' => $user->id,
                    'status' => 'waiting',
                    'waiting_position' => $nextPosition,
                ]);
            } else {
                Registration::create([
                    'workshop_id' => $workshop->id,
                    'user_id' => $user->id,
                    'status' => 'confirmed',
                ]);
            }
        });

        return back()->with('success', 'Successfully registered.');
    }

    public function destroy(Request $request, Workshop $workshop)
    {
        $user = $request->user();

        $registration = $workshop->registrations()
            ->where('user_id', $user->id)
            ->firstOrFail();

        DB::transaction(function () use ($registration, $workshop) {
            $wasConfirmed = $registration->status === 'confirmed';
            $registration->delete();

            if ($wasConfirmed) {
                // Promote the first person on the waiting list
                $next = $workshop->waitingRegistrations()->first();
                if ($next) {
                    $next->update([
                        'status' => 'confirmed',
                        'waiting_position' => null,
                    ]);

                    // Re-number remaining waiting list
                    $workshop->waitingRegistrations()
                        ->orderBy('waiting_position')
                        ->get()
                        ->each(function ($reg, $index) {
                            $reg->update(['waiting_position' => $index + 1]);
                        });
                }
            } else {
                // Re-number remaining waiting list
                $workshop->waitingRegistrations()
                    ->orderBy('waiting_position')
                    ->get()
                    ->each(function ($reg, $index) {
                        $reg->update(['waiting_position' => $index + 1]);
                    });
            }
        });

        return back()->with('success', 'Registration cancelled.');
    }
}
