# Notes Manager (Laravel)

A bilingual (Ukrainian/English) Notes CRUD built with Laravel. Includes search, sorting, pagination, inline creation, and bulk delete.

## Setup

1. Install dependencies:
   ```bash
   composer install
   ```
2. Copy the environment template and configure your database connection (MySQL, SQLite, etc.):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Run the notes migration to create the `notes` table:
   ```bash
   php artisan migrate
   ```
4. Start the dev server:
   ```bash
   php artisan serve
   ```

If you switch databases after migrating, rerun migrations (or `php artisan migrate:fresh`) so the `notes` table exists in the active connection.
