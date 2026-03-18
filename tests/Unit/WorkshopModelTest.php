<?php

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

test('workshop reports correct available seats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $workshop = Workshop::create([
        'created_by' => $admin->id,
        'title' => 'Test',
        'description' => 'desc',
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
        'capacity' => 3,
    ]);

    expect($workshop->available_seats)->toBe(3);

    $users = User::factory(2)->create(['role' => 'employee']);
    foreach ($users as $user) {
        Registration::create([
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);
    }

    expect($workshop->available_seats)->toBe(1);
});

test('workshop is full when confirmed registrations reach capacity', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $workshop = Workshop::create([
        'created_by' => $admin->id,
        'title' => 'Full Workshop',
        'description' => 'desc',
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
        'capacity' => 2,
    ]);

    expect($workshop->isFull())->toBeFalse();

    $users = User::factory(2)->create(['role' => 'employee']);
    foreach ($users as $user) {
        Registration::create([
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);
    }

    expect($workshop->fresh()->isFull())->toBeTrue();
});

test('user isAdmin returns true only for admin role', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'employee']);

    expect($admin->isAdmin())->toBeTrue();
    expect($employee->isAdmin())->toBeFalse();
});
