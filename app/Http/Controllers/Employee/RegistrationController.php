<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkshopWithRegistrationResource;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $workshops = Workshop::where('start_time', '>', now())
            ->withCount('confirmedRegistrations')
            ->with(['registrations' => fn ($q) => $q->where('user_id', $user->id)])
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Employee/Dashboard', [
            'workshops' => $workshops->map(
                fn ($workshop) => new WorkshopWithRegistrationResource(
                    $workshop,
                    $workshop->registrations->first()
                )
            ),
        ]);
    }

    public function store(Request $request, Workshop $workshop)
    {
        $error = $this->registrationService->register($request->user(), $workshop);

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Successfully registered.');
    }

    public function destroy(Request $request, Workshop $workshop)
    {
        $registration = $workshop->registrations()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $this->registrationService->cancel($registration);

        return back()->with('success', 'Registration cancelled.');
    }
}
