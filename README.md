# Internal Academy

A web application for managing company workshops and registrations.

**Stack:** Laravel 12 · Vue 3 · Inertia.js · SQLite · Pest

---

## Table of Contents

- [Quick Start](#quick-start)
- [Test Data (Seeder)](#test-data-seeder)
- [Running Tests](#running-tests)
- [Custom Artisan Command](#custom-artisan-command)
- [Feature Overview](#feature-overview)
- [Architectural Decisions](#architectural-decisions)

---

## Quick Start

### 1. Clone & install dependencies

```bash
git clone <your-repo-url>
cd internal-academy

composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

> The default `.env` uses SQLite — no database configuration required.

### 3. Create the database & run migrations

```bash
touch database/database.sqlite
php artisan migrate
```

### 4. Seed test data

```bash
php artisan db:seed
```

See [Test Data (Seeder)](#test-data-seeder) for what gets created and the login credentials.

### 5. Build frontend assets

```bash
npm run build
# or for development with hot-module replacement:
npm run dev
```

### 6. Start the server

```bash
php artisan serve
```

Visit <http://localhost:8000> and log in as Admin or Employee.

---

## Test Data (Seeder)

Running `php artisan db:seed` creates a realistic dataset so you can explore every feature immediately.

### Users

| Role     | Email              | Password |
|----------|--------------------|----------|
| Admin    | admin@academy.test | password |
| Employee | *(10 random)*      | password |

To list employee emails:

```bash
php artisan tinker
```

```php
App\Models\User::where('role', 'employee')->pluck('email');
```

### Workshops

Five sample workshops are created with varied capacities and schedules:

| Title                                 | Capacity | Pre-seeded state                       |
|---------------------------------------|----------|----------------------------------------|
| Clean Code & SOLID Principles         | 3        | 3 confirmed + 2 on waiting list (full) |
| Docker & CI/CD Pipelines              | 2        | 2 confirmed (full)                     |
| Introduction to Domain-Driven Design  | 15       | open                                   |
| Vue 3 & Inertia.js Deep Dive          | 10       | open                                   |
| Agile & Scrum in Practice             | 20       | open                                   |

This lets you immediately see the waiting list, fill-rate progress bars, and available seats in action.

### Reset & re-seed

To wipe all data and start fresh:

```bash
php artisan migrate:fresh --seed
```

---

## Running Tests

Tests use an **in-memory SQLite** database (`:memory:`) and the `RefreshDatabase` trait — each test starts with a clean state.

### Run the full suite

```bash
php artisan test
```

### Run only the academy tests

```bash
php artisan test tests/Feature/WorkshopTest.php tests/Unit/WorkshopModelTest.php
```

### Test breakdown (47 total)

- **`tests/Feature/WorkshopTest.php`** — 19 tests: admin CRUD, role access, employee registration, waiting list, overlap prevention, past-workshop guard, capacity floor, promotion overlap safety, stats endpoint, artisan remind
- **`tests/Unit/WorkshopModelTest.php`** — 3 tests: `available_seats`, `isFull()`, `isAdmin()`
- **`tests/Feature/` *(Breeze)*** — 25 tests: authentication, registration, password reset, email verification, profile

---

## Custom Artisan Command

Send reminder emails to all confirmed participants of workshops scheduled for **tomorrow**:

```bash
php artisan academy:remind
```

**Output example:**

```
  ✉ Sent reminder to alice@example.com for "Clean Code & SOLID Principles"
  ✉ Sent reminder to bob@example.com for "Clean Code & SOLID Principles"
Done. 2 reminder(s) sent for 1 workshop(s).
```

Emails are sent via the configured mail driver (`MAIL_MAILER` in `.env`). In local environments this defaults to `log` — check `storage/logs/laravel.log` to inspect the generated emails.

---

## Feature Overview

### Must Have

- **Authentication** — Laravel Breeze with Inertia + Vue
- **Role-based access** — `admin` and `employee` roles; `EnsureAdmin` middleware protects all `/admin` routes
- **Workshop CRUD** (Admin) — create, edit, and delete workshops with title, description, start/end time, and capacity
- **Registration** (Employee) — one-click register/cancel for upcoming workshops

### Nice to Have

- **Waiting list** — FIFO queue when a workshop is full; the first *eligible* waiting user is automatically promoted on cancellation
- **No-ubiquity check** — prevents registering for two workshops that overlap in time
- **Artisan reminder** — `php artisan academy:remind`

### Top Player Zone

- **Admin statistics dashboard** — most popular workshop, registration counts, fill-rate progress bars
- **Real-time updates** — the stats page polls `/admin/stats` every 5 seconds (no page refresh needed)
- **47 Pest tests** — feature tests for all business rules + unit tests for model logic

---

## Architectural Decisions

### Why polling instead of WebSockets/Reverb?

A 5-second polling interval gives a good-enough "real-time" experience for a statistics dashboard without the operational overhead of running a WebSocket server (Redis, Reverb process, etc.). For a code challenge scope this is the right trade-off; switching to Reverb later is a localised change in `Admin/Dashboard.vue`.

### Why SQLite?

Zero setup for reviewers — clone, migrate, run. MySQL support is a one-line change in `.env`.

### Why a simple `role` column instead of a policy/gate system?

Two roles, simple access patterns. The `EnsureAdmin` middleware + `isAdmin()` helper on `User` keeps it readable without pulling in `spatie/laravel-permission`. If roles grow, extracting to a proper RBAC layer is straightforward.

### Registration concurrency

The duplicate-registration check, the overlap check, and the capacity check all run **inside** `DB::transaction()` after acquiring a row-level lock with `lockForUpdate()` on the workshop. This serialises concurrent requests so two users racing for the last seat cannot both pass the checks and over-fill the workshop. The unique constraint on `(workshop_id, user_id)` in the database is an additional safety net.

### Waiting list promotion

When a confirmed participant cancels, the code promotes the first waiting user who has **no overlapping confirmed workshop** — not simply the first in queue. This preserves the no-ubiquity guarantee after promotion. Waiting positions are then re-indexed from 1.

### Capacity floor on edit

When an admin edits a workshop, the capacity field is validated with `min:<confirmed_count>`. This prevents setting capacity below the number of already-confirmed participants, which would create inconsistent state (negative available seats).
