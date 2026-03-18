# Internal Academy

A web application for managing company workshops and registrations.

**Stack:** Laravel 12 · Vue 3 · Inertia.js · SQLite · Pest

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

### 3. Run migrations & seed

```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

**Seeded users:**

| Role     | Email                   | Password  |
|----------|-------------------------|-----------|
| Admin    | admin@academy.test      | password  |
| Employee | (10 random users)       | password  |

Employee emails are visible in the database or via `php artisan tinker`:

```php
App\Models\User::where('role', 'employee')->pluck('email');
```

### 4. Build frontend assets

```bash
npm run build
# or for development with HMR:
npm run dev
```

### 5. Start the server

```bash
php artisan serve
```

Visit <http://localhost:8000> — log in as Admin or Employee.

---

## Running Tests

```bash
php artisan test
```

Tests use an in-memory SQLite database (`:memory:`) and refresh between each test via `RefreshDatabase`.

Run only the academy tests:

```bash
php artisan test tests/Feature/WorkshopTest.php tests/Unit/WorkshopModelTest.php
```

---

## Custom Artisan Command

Send reminder emails to all confirmed participants of workshops scheduled for **tomorrow**:

```bash
php artisan academy:remind
```

Emails are sent via the configured mail driver (`MAIL_MAILER` in `.env`). In local/test environments this defaults to `log` — check `storage/logs/laravel.log` to inspect the generated emails.

---

## Feature Overview

### Must Have

- **Authentication** — Laravel Breeze with Inertia + Vue
- **Role-based access** — `admin` and `employee` roles on the `users` table
- **Workshop CRUD** (Admin) — create, edit, delete workshops with title, description, start/end time, and capacity
- **Registration** (Employee) — one-click register/cancel for upcoming workshops

### Nice to Have

- **Waiting list** — FIFO queue when a workshop is full; first waiting user is automatically promoted on cancellation
- **No-ubiquity check** — prevents registering for two workshops that overlap in time
- **Artisan reminder** — `php artisan academy:remind`

### Top Player Zone

- **Admin statistics dashboard** — most popular workshop, registration counts, fill-rate progress bars
- **Real-time updates** — the stats page polls `/admin/stats` every 5 seconds (no page refresh needed)
- **40 Pest tests** — feature tests for all business rules + unit tests for model logic

---

## Architectural Decisions

### Why polling instead of WebSockets/Reverb?

A 5-second polling interval gives a good-enough "real-time" experience for a statistics dashboard without the operational overhead of running a WebSocket server (Redis, Reverb process, etc.). For a code challenge scope this is the right trade-off; switching to Reverb later is a localised change in `Admin/Dashboard.vue`.

### Why SQLite?

Zero setup for reviewers — clone, migrate, run. MySQL support is a one-line change in `.env`.

### Why a simple `role` column instead of a policy/gate system?

Two roles, simple access patterns. The `EnsureAdmin` middleware + `isAdmin()` helper on `User` keeps it readable without pulling in `spatie/laravel-permission`. If roles grow, extracting to a proper RBAC layer is straightforward.

### Registration concurrency

The `store` and `destroy` actions on `RegistrationController` are wrapped in `DB::transaction()` to prevent race conditions (e.g., two users registering for the last seat simultaneously). The unique constraint on `(workshop_id, user_id)` in the database also acts as a safety net.

### Waiting list re-numbering

Positions are integers stored on the `registrations` table and re-indexed after every cancellation. An alternative would be to rely on `created_at` ordering; integer positions are slightly more explicit and make the "position #N" display trivial.
