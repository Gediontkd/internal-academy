<?php

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function makeEmployee(): User
{
    return User::factory()->create(['role' => 'employee']);
}

function makeWorkshop(array $attrs = []): Workshop
{
    $admin = User::factory()->create(['role' => 'admin']);
    return Workshop::factory()->create(array_merge(['created_by' => $admin->id], $attrs));
}

// ── Admin: workshop CRUD ───────────────────────────────────────────────────────

test('admin can view workshops list', function () {
    $admin = makeAdmin();
    makeWorkshop(['title' => 'DDD Workshop']);

    $this->actingAs($admin)
        ->get(route('admin.workshops.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Workshops/Index')
            ->has('workshops', 1)
        );
});

test('employee cannot access admin workshops list', function () {
    $employee = makeEmployee();

    $this->actingAs($employee)
        ->get(route('admin.workshops.index'))
        ->assertForbidden();
});

test('admin can create a workshop', function () {
    $admin = makeAdmin();

    $this->actingAs($admin)
        ->post(route('admin.workshops.store'), [
            'title' => 'New Workshop',
            'description' => 'A great workshop',
            'start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 20,
        ])
        ->assertRedirect(route('admin.workshops.index'));

    $this->assertDatabaseHas('workshops', ['title' => 'New Workshop']);
});

test('workshop creation requires a title', function () {
    $admin = makeAdmin();

    $this->actingAs($admin)
        ->post(route('admin.workshops.store'), [
            'description' => 'Missing title',
            'start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 10,
        ])
        ->assertSessionHasErrors('title');
});

test('admin can delete a workshop', function () {
    $admin = makeAdmin();
    $workshop = makeWorkshop();

    $this->actingAs($admin)
        ->delete(route('admin.workshops.destroy', $workshop))
        ->assertRedirect(route('admin.workshops.index'));

    $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
});

// ── Employee: registration ─────────────────────────────────────────────────────

test('employee can see upcoming workshops', function () {
    $employee = makeEmployee();
    makeWorkshop(['start_time' => now()->addDay(), 'end_time' => now()->addDay()->addHours(2)]);

    $this->actingAs($employee)
        ->get(route('employee.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Employee/Dashboard')->has('workshops', 1));
});

test('employee can register for a workshop with available seats', function () {
    $employee = makeEmployee();
    $workshop = makeWorkshop([
        'capacity' => 5,
        'start_time' => now()->addDays(2),
        'end_time' => now()->addDays(2)->addHours(2),
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.register', $workshop))
        ->assertRedirect();

    $this->assertDatabaseHas('registrations', [
        'workshop_id' => $workshop->id,
        'user_id' => $employee->id,
        'status' => 'confirmed',
    ]);
});

test('employee is placed on waiting list when workshop is full', function () {
    $employee = makeEmployee();
    $workshop = makeWorkshop([
        'capacity' => 1,
        'start_time' => now()->addDays(2),
        'end_time' => now()->addDays(2)->addHours(2),
    ]);

    // Fill the workshop
    $other = makeEmployee();
    Registration::create([
        'workshop_id' => $workshop->id,
        'user_id' => $other->id,
        'status' => 'confirmed',
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.register', $workshop))
        ->assertRedirect();

    $this->assertDatabaseHas('registrations', [
        'workshop_id' => $workshop->id,
        'user_id' => $employee->id,
        'status' => 'waiting',
    ]);
});

test('employee cannot register twice for the same workshop', function () {
    $employee = makeEmployee();
    $workshop = makeWorkshop([
        'capacity' => 5,
        'start_time' => now()->addDays(2),
        'end_time' => now()->addDays(2)->addHours(2),
    ]);

    Registration::create([
        'workshop_id' => $workshop->id,
        'user_id' => $employee->id,
        'status' => 'confirmed',
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.register', $workshop))
        ->assertSessionHas('error');
});

test('employee cannot register for overlapping workshops', function () {
    $employee = makeEmployee();
    $admin = makeAdmin();

    $workshop1 = Workshop::create([
        'created_by' => $admin->id,
        'title' => 'Workshop A',
        'description' => 'desc',
        'start_time' => now()->addDays(2)->setTime(10, 0),
        'end_time' => now()->addDays(2)->setTime(12, 0),
        'capacity' => 10,
    ]);

    $workshop2 = Workshop::create([
        'created_by' => $admin->id,
        'title' => 'Workshop B',
        'description' => 'desc',
        'start_time' => now()->addDays(2)->setTime(11, 0),
        'end_time' => now()->addDays(2)->setTime(13, 0),
        'capacity' => 10,
    ]);

    Registration::create([
        'workshop_id' => $workshop1->id,
        'user_id' => $employee->id,
        'status' => 'confirmed',
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.register', $workshop2))
        ->assertSessionHas('error');
});

test('cancelling a confirmed registration promotes the first waiting-list user', function () {
    $workshop = makeWorkshop([
        'capacity' => 1,
        'start_time' => now()->addDays(2),
        'end_time' => now()->addDays(2)->addHours(2),
    ]);

    $confirmed = makeEmployee();
    $waiting = makeEmployee();

    Registration::create([
        'workshop_id' => $workshop->id,
        'user_id' => $confirmed->id,
        'status' => 'confirmed',
    ]);
    Registration::create([
        'workshop_id' => $workshop->id,
        'user_id' => $waiting->id,
        'status' => 'waiting',
        'waiting_position' => 1,
    ]);

    $this->actingAs($confirmed)
        ->delete(route('employee.workshops.unregister', $workshop))
        ->assertRedirect();

    $this->assertDatabaseHas('registrations', [
        'workshop_id' => $workshop->id,
        'user_id' => $waiting->id,
        'status' => 'confirmed',
    ]);
});

test('employee can cancel their registration', function () {
    $employee = makeEmployee();
    $workshop = makeWorkshop([
        'capacity' => 5,
        'start_time' => now()->addDays(2),
        'end_time' => now()->addDays(2)->addHours(2),
    ]);

    Registration::create([
        'workshop_id' => $workshop->id,
        'user_id' => $employee->id,
        'status' => 'confirmed',
    ]);

    $this->actingAs($employee)
        ->delete(route('employee.workshops.unregister', $workshop))
        ->assertRedirect();

    $this->assertDatabaseMissing('registrations', [
        'workshop_id' => $workshop->id,
        'user_id' => $employee->id,
    ]);
});
