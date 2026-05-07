# IT Store
IT Store is a Laravel-based internal technical support library for publishing apps, scripts, and documents with role-based access control.

## Features
- Public resource catalog for anonymous users (apps/scripts/docs, descriptions, latest updates, download/open link).
- Private resources visible only to authorized logged-in users.
- Resource source types:
  - Local file upload (stored on Laravel local disk).
  - Internal or external link.
- Admin resource management (create/update/delete).
- Dynamic role management (create/edit/delete roles).
- Dynamic permission assignment to roles.
- User role assignment management.
- SQLite database by default.
- Production Docker setup with persistent volumes.

## Access model and permissions
Default permissions seeded by `PermissionSeeder`:
- `resources.view_private`
- `resources.manage`
- `roles.manage`
- `users.manage_roles`

Default roles seeded:
- `admin` (all permissions)
- `member` (`resources.view_private`)

Default admin user is created from `.env` values:
- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

## Local development (without Docker)
1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Create environment file and app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Create SQLite file and run migrations + seeders:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```
4. Build assets and run:
   ```bash
   npm run build
   php artisan serve
   ```

## Production Docker deployment
### First-time setup
1. Create `.env` from example and update values:
   ```bash
   cp .env.example .env
   ```
2. Build and start containers:
   ```bash
   docker compose up -d --build
   ```
3. Open app:
   - `http://localhost:8080`

### Runtime behavior in container
On startup, the container entrypoint will:
- Ensure required writable directories exist.
- Ensure `database/database.sqlite` exists.
- Generate `APP_KEY` if missing.
- Run migrations when `RUN_MIGRATIONS=true`.
- Run seeders when `RUN_SEEDERS=true`.
- Cache config and compiled views.

## Persistent data and config
`docker-compose.yml` mounts persistent storage so updates do not erase data:
- `it_store_storage` → `/var/www/html/storage`
- `it_store_database` → `/var/www/html/database`
- `it_store_bootstrap_cache` → `/var/www/html/bootstrap/cache`
- `./.env` bind mount → `/var/www/html/.env`

This keeps uploaded files, SQLite data, cache artifacts, and environment configuration outside the ephemeral container layer.

## Safe update process (new image/container)
1. Keep existing `.env` and Docker volumes.
2. Rebuild and recreate:
   ```bash
   docker compose down
   docker compose up -d --build
   ```
3. Data and config remain because they are mounted volumes/files, not container filesystem state.

## Notes
- Uploaded files are stored locally (not external object storage).
- Link resources accept:
  - Internal paths starting with `/`
  - External `http://` or `https://` URLs
- After first bootstrap, set `RUN_SEEDERS=false` in `.env` to avoid reseeding on every restart.
