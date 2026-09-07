# Laravel Enterprise Kit

> Production-minded Laravel 13 foundation for internal tools, business systems, admin portals and enterprise applications.

[![Laravel Quality](https://github.com/ayagaidi/laravel-enterprise-kit/actions/workflows/tests.yml/badge.svg)](https://github.com/ayagaidi/laravel-enterprise-kit/actions/workflows/tests.yml)

**RBAC · Audit Trail · System Settings · Sanctum API · Arabic/English · Tests · CI**

Laravel Enterprise Kit is intentionally not another CRUD demo. It starts with the concerns that become expensive to retrofit later: authorization, traceability, safe configuration, API boundaries, localization and automated quality gates.

## v0.1 foundation

- session authentication with login rate limiting
- active/disabled user enforcement
- roles and permissions with least-privilege middleware
- immutable `super-admin` role behavior
- user management with role assignment
- system settings with cache invalidation
- append-only audit trail with actor, IP, user agent and request ID
- audit payload redaction for common secret fields
- Laravel Sanctum personal-access-token API
- versioned `/api/v1` routes
- public/private settings separation
- Arabic/English locale switching with RTL/LTR layout
- Feature tests and Laravel Pint in GitHub Actions
- demo seeding only when `SEED_ADMIN_PASSWORD` is explicitly provided

## Tech stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum 4.x
- SQLite by default; MySQL/PostgreSQL friendly
- Bootstrap 5 admin UI
- PHPUnit 12
- Laravel Pint

## Quick start

```bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

To seed a local demo administrator, set a non-production password first:

```env
SEED_ADMIN_EMAIL=admin@example.test
SEED_ADMIN_PASSWORD=change-this-locally
```

Then run `php artisan db:seed`.

> No admin password is committed to the repository.

## Authorization contract

Permissions use stable slugs such as:

```text
dashboard.view
users.view
users.create
users.update
roles.manage
audit.view
settings.manage
```

Routes declare permissions explicitly:

```php
Route::get('/audit-logs', [AuditLogController::class, 'index'])
    ->middleware('permission:audit.view');
```

The `super-admin` role bypasses permission lookup by design. Other roles receive explicit permissions.

## Audit design

Mutating admin actions write append-only audit events containing:

- actor user ID
- action name
- subject type / ID
- old/new values
- IP address
- user agent
- request UUID
- timestamp

Known secret keys such as passwords, tokens and secrets are redacted before storage.

## API

Public configuration:

```http
GET /api/v1/settings/public
```

Authenticated profile:

```http
GET /api/v1/me
Authorization: Bearer <sanctum-token>
```

## Quality

```bash
php artisan test
vendor/bin/pint --test
```

The initial suite covers authentication, disabled accounts, RBAC enforcement, audit logging, settings changes, public/private API settings, Sanctum authentication and locale switching.

## Architecture

See [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) and [`ROADMAP.md`](ROADMAP.md).

## Security

See [`SECURITY.md`](SECURITY.md). Never open a public issue for a suspected vulnerability.

## License

MIT. See [`LICENSE`](LICENSE).

Maintained by **Aya Aljaidi** — Laravel / Full-Stack Developer, Tripoli, Libya.
