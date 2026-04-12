<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkshopResource;
use App\Models\Registration;
use App\Models\Workshop;
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

        $workshops = Workshop::withCount(['confirmedRegistrations', 'waitingRegistrations'])
            ->orderBy('start_time')
            ->get();

        return [
            'total_workshops'     => Workshop::count(),
            'total_registrations' => Registration::where('status', 'confirmed')->count(),
            'total_waiting'       => Registration::where('status', 'waiting')->count(),
            'most_popular'        => $mostPopular ? WorkshopResource::make($mostPopular) : null,
            'workshops'           => WorkshopResource::collection($workshops),
        ];
    }
}
