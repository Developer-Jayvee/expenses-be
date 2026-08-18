# Expenses Backend

Laravel 13 (PHP 8.3) JSON API for the Expenses budgeting app. Uses Sanctum
cookie-based authentication (not bearer tokens) and MySQL. Pair it with the
`expenses-app/` React frontend for the full application.

## Requirements

- PHP 8.3+
- Composer
- MySQL (or another Eloquent-supported database)
- Node.js and npm (only needed for `composer run dev`, which also boots the
  frontend's Vite dev server via `concurrently`)

## Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Create your environment file and generate the app key:

```bash
cp .env.example .env   # if present; otherwise create .env manually
php artisan key:generate
```

3. Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=budgetDB
DB_USERNAME=root
DB_PASSWORD=
```

4. Run migrations and seeders:

```bash
php artisan migrate --seed
```

5. Start the API server:

```bash
php artisan serve
```

The API is available at `http://127.0.0.1:8000`.

Alternatively, `composer run dev` starts the Laravel server, queue listener,
`pail` log tailing, and the frontend's Vite dev server together (requires
`expenses-app/` to be a sibling directory with its own dependencies
installed).

## Frontend

Run `expenses-app/` separately and point its `VITE_BASE_URL_API` /
`VITE_BASE_URL` env vars at this backend:

```env
VITE_BASE_URL_API=http://localhost:8000/api
VITE_BASE_URL=http://localhost:8000
```

Authenticated routes use the `auth.cookie` middleware alias (Sanctum cookie
auth), so the frontend must send requests with credentials included.

## Architecture

- `app/Http/Controllers/` — controllers extend `BaseCrudController`, which
  supplies generic `index` / `storeQuery` / `showQuery` / `updateQuery` /
  `destroyQuery` methods driven by `$baseModel`, `$storeRequest`,
  `$updateRequest`, and `$resource`. Each controller implements
  `setupParams()` to wire those up.
- `app/Services/` — business logic extends `BaseCrudService`. Controllers
  stay thin and delegate to a service rather than querying models directly.
- `app/Http/Requests/` — validation via `FormRequest` classes extending
  `BaseFormRequest`.
- `app/Http/Resources/` — API response shaping.
- `app/Models/` — Eloquent models (note the `*Model` suffix convention,
  e.g. `BillsModel`, `TransactionsModel`).
- `app/Enums/` — typed value sets (bill categories/frequency/status,
  payment types, activity types, daily budget/expense types).
- `app/Traits/` — cross-cutting helpers mixed into controllers/services:
  `ErrorMessageTrait` / `SuccessMessageTrait` (uniform JSON envelope),
  `LoggerTrait`, `UtilitiesTrait`.
- `app/Contracts/` + `app/Services/ActivityLogger.php` — activity logging
  is defined behind `ActivityLoggerInterface` and injected where needed.
- `app/Helpers/DateHelper.php` — shared date utilities.
- `app/Http/Middleware/AuthMiddleware.php` — registered as the
  `auth.cookie` alias in `bootstrap/app.php`.

### API surface (`routes/api.php`)

All routes below (except `login`) sit inside the `auth.cookie` middleware
group:

- **Bills** — `GET/POST /bills`, `GET /bills/{id}/details`,
  `PATCH /bills/{id}/update`, `DELETE /bills/{id}/delete`,
  `GET /bills/{bill}/nextBill`, `GET /bills/{bill}/activities`
- **Transactions** — `GET /transaction/{bill}/list`,
  `POST /transaction/create`, `DELETE /transaction/{id}/delete`
- **Daily budgets/expenses** — `GET/POST /daily-budgets`,
  `GET /daily-budgets/active`, `GET /daily-budgets/{id}/details`,
  `PATCH /daily-budgets/{id}/done`, `PATCH /daily-budgets/{id}/cancel`,
  `POST /daily-budgets/{budget}/expenses`,
  `DELETE /daily-budgets/expenses/{id}/delete`
- **Dashboard** — `GET /dashboard/summary` (monthly expenses, bills by
  category, upcoming bills)
- **Options** — `GET /options/{type}` (reference/lookup data for forms)
- **Auth** — `POST /login`, `POST /logout`, `GET /auth-check`,
  `GET /user` (Sanctum session route)

## Run tests

```bash
php artisan test        # PHPUnit — tests/Feature and tests/Unit
```

## Code style

```bash
./vendor/bin/pint
```
