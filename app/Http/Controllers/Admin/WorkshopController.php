<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function index(): Response
    {
        $workshops = Workshop::with('creator')
            ->withCount(['confirmedRegistrations', 'waitingRegistrations'])
            ->latest()
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'title' => $w->title,
                'description' => $w->description,
                'start_time' => $w->start_time->toISOString(),
                'end_time' => $w->end_time->toISOString(),
                'capacity' => $w->capacity,
                'confirmed_count' => $w->confirmed_registrations_count,
                'waiting_count' => $w->waiting_registrations_count,
                'available_seats' => max(0, $w->capacity - $w->confirmed_registrations_count),
            ]);

        return Inertia::render('Admin/Workshops/Index', [
            'workshops' => $workshops,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Workshops/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        Workshop::create(array_merge($validated, [
            'created_by' => $request->user()->id,
        ]));

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop created successfully.');
    }

    public function edit(Workshop $workshop): Response
    {
        return Inertia::render('Admin/Workshops/Form', [
            'workshop' => [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'description' => $workshop->description,
                'start_time' => $workshop->start_time->format('Y-m-d\TH:i'),
                'end_time' => $workshop->end_time->format('Y-m-d\TH:i'),
                'capacity' => $workshop->capacity,
            ],
        ]);
    }

    public function update(Request $request, Workshop $workshop)
    {
        $confirmedCount = $workshop->confirmedRegistrations()->count();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'capacity' => "required|integer|min:{$confirmedCount}",
        ], [
            'capacity.min' => "Capacity cannot be less than the number of confirmed participants ({$confirmedCount}).",
        ]);

        $workshop->update($validated);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop deleted.');
    }
}
