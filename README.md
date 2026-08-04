# Expenses Backend

This repository contains the Laravel backend for the Expenses app. Use it together with the React frontend repository for the full application experience.

## Requirements

- PHP 8.2 or higher
- Composer
- A database such as MySQL, PostgreSQL, or SQLite
- Node.js and npm for the React frontend

## Backend setup

1. Clone the repository and move into the project folder:

```bash
git clone <repo-url>
cd expenses-be
```

2. Install PHP dependencies:

```bash
composer install
```

3. Create your environment file:

```bash
cp .env.example .env
```

4. Generate the application key:

```bash
php artisan key:generate
```

5. Configure your database in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expenses
DB_USERNAME=root
DB_PASSWORD=
```

6. Run the database migrations and seeders:

```bash
php artisan migrate --seed
```

7. Start the Laravel development server:

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

## Frontend setup

Use the React frontend repository for the client application. Keep this Laravel backend running, then point the frontend to the API URL:

```env
VITE_API_URL=http://127.0.0.1:8000
```

If you are using the expenses-app frontend repo, run it separately and ensure it calls this backend API.

## Run tests

```bash
php artisan test
```
