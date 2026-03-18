<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => $this->getStats(),
        ]);
    }

    public function stats(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->getStats());
    }

    private function getStats(): array
    {
        $mostPopular = Workshop::withCount('confirmedRegistrations')
            ->orderByDesc('confirmed_registrations_count')
            ->first();

        $workshopsWithCounts = Workshop::withCount(['confirmedRegistrations', 'waitingRegistrations'])
            ->orderBy('start_time')
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'title' => $w->title,
                'start_time' => $w->start_time->toISOString(),
                'capacity' => $w->capacity,
                'confirmed_count' => $w->confirmed_registrations_count,
                'waiting_count' => $w->waiting_registrations_count,
            ]);

        return [
            'total_workshops' => Workshop::count(),
            'total_registrations' => Registration::where('status', 'confirmed')->count(),
            'total_waiting' => Registration::where('status', 'waiting')->count(),
            'most_popular' => $mostPopular ? [
                'id' => $mostPopular->id,
                'title' => $mostPopular->title,
                'confirmed_count' => $mostPopular->confirmed_registrations_count,
            ] : null,
            'workshops' => $workshopsWithCounts,
        ];
    }
}
