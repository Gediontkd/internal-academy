<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@academy.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Employee users
        $employees = User::factory(10)->create([
            'role' => 'employee',
        ]);

        // Sample workshops
        $workshops = [
            [
                'title' => 'Introduction to Domain-Driven Design',
                'description' => 'Learn the fundamentals of DDD, bounded contexts, and aggregates.',
                'start_time' => now()->addDays(1)->setTime(10, 0),
                'end_time' => now()->addDays(1)->setTime(12, 0),
                'capacity' => 20,
            ],
            [
                'title' => 'Clean Code & SOLID Principles',
                'description' => 'Practical workshop on writing maintainable, readable code.',
                'start_time' => now()->addDays(2)->setTime(14, 0),
                'end_time' => now()->addDays(2)->setTime(16, 0),
                'capacity' => 3,
            ],
            [
                'title' => 'Vue.js 3 Composition API Deep Dive',
                'description' => 'Advanced patterns with Composition API, composables and reactivity.',
                'start_time' => now()->addDays(3)->setTime(9, 0),
                'end_time' => now()->addDays(3)->setTime(11, 0),
                'capacity' => 15,
            ],
            [
                'title' => 'Docker & CI/CD Pipelines',
                'description' => 'Containerise your apps and automate deployments.',
                'start_time' => now()->addDays(5)->setTime(10, 0),
                'end_time' => now()->addDays(5)->setTime(13, 0),
                'capacity' => 2,
            ],
            [
                'title' => 'Agile Retrospective Techniques',
                'description' => 'Non-technical workshop on team dynamics and retrospective formats.',
                'start_time' => now()->addDays(7)->setTime(15, 0),
                'end_time' => now()->addDays(7)->setTime(17, 0),
                'capacity' => 30,
            ],
        ];

        foreach ($workshops as $data) {
            Workshop::create(array_merge($data, ['created_by' => $admin->id]));
        }

        // Seed some registrations for the "full" workshop (capacity 3) to demonstrate waiting list
        $fullWorkshop = Workshop::where('title', 'Clean Code & SOLID Principles')->first();
        $dockerWorkshop = Workshop::where('title', 'Docker & CI/CD Pipelines')->first();

        foreach ($employees->take(3) as $i => $employee) {
            Registration::create([
                'workshop_id' => $fullWorkshop->id,
                'user_id' => $employee->id,
                'status' => 'confirmed',
            ]);
        }

        // Two more go to waiting list
        foreach ($employees->slice(3, 2) as $i => $employee) {
            Registration::create([
                'workshop_id' => $fullWorkshop->id,
                'user_id' => $employee->id,
                'status' => 'waiting',
                'waiting_position' => $i + 1,
            ]);
        }

        // Fill Docker workshop (capacity 2)
        foreach ($employees->take(2) as $employee) {
            Registration::create([
                'workshop_id' => $dockerWorkshop->id,
                'user_id' => $employee->id,
                'status' => 'confirmed',
            ]);
        }

        $this->command->info('Seeded successfully!');
        $this->command->info('Admin: admin@academy.test / password');
        $this->command->info('Employees: check database for user emails (password: "password")');
    }
}
