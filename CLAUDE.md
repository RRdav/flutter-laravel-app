# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

# softeng-app-backend

Laravel API backend for a self-built software engineering learning app (mobile via Flutter, desktop via NativePHP — this repo only covers the backend).

## Stack

- Laravel (PHP 8.4+, check `php -v` if composer install complains about version mismatches)
- PostgreSQL (local dev, not SQLite — `.env` must have `DB_CONNECTION=pgsql`)
- Sanctum for API auth (installed via `php artisan install:api`)
- Pest/PHPUnit for tests
- Laravel Pint for code style

## Commands

- Run locally: `php artisan serve`
- Run migrations: `php artisan migrate`
- Run tests: `php artisan test` (or `composer test`, which clears the config cache first)
- Run a single test: `php artisan test --filter=TestName` or `php artisan test tests/Feature/SomeTest.php`
- First-time setup: `composer setup` (installs deps, copies `.env.example`, generates key, migrates, builds frontend assets)
- Fix code style: `vendor/bin/pint` (CI runs `vendor/bin/pint --test`, which only checks — always run the plain version locally before committing)
- Generate IDE helper file after adding new facades/models: `php artisan ide-helper:generate`

## Conventions

- Models: singular PascalCase (`Topic`, `Lesson`), tables: plural snake_case (`topics`, `lessons`) — rely on Eloquent's default convention, don't override table names manually unless there's a real reason.
- Controllers: resourceful, generated with `--api` (not `-r`) to skip `create`/`edit` HTML-form stubs we don't need for a JSON API.
- Routes: `Route::apiResource()` in `routes/api.php`, not `routes/web.php`.
- Always validate request input in `store`/`update` methods before touching the database.
- Follow `TopicController` as the reference pattern: inline `$request->validate()`, `store` returns `response()->json($model, 201)`, `destroy` returns `response()->json(null, 204)`, route-model binding for `show`/`update`/`destroy`. Models use `$fillable` for mass assignment and declare relationships as methods (e.g. `Topic::lessons()`).

## Testing

- `phpunit.xml` forces `DB_CONNECTION=sqlite` with `:memory:` for local `php artisan test`, so tests do not hit the local Postgres. CI overrides this with a Postgres 16 service via env vars — avoid SQLite-incompatible or Postgres-only schema/queries without checking both.
- `RefreshDatabase` is commented out in `tests/Pest.php`; enable it (or `pest()->use(...)`) before writing Feature tests that touch the database. Only the starter example tests exist so far.

## Data model (in progress)

- `topics` — done (migration, model, controller, routes all working)
- `lessons` — belongs to `topics` via `topic_id` FK (next up). Currently only scaffolded: the migration has no columns beyond `id`/timestamps (no `topic_id` yet), `Lesson` model is empty (no `$fillable`, no `topic()` relation), `LessonController` is empty stubs that still include `create`/`edit` (delete them per the `--api` convention), and no route is registered in `routes/api.php`. `Topic::lessons()` already references `Lesson`.
- `questions` — will belong to `lessons` via `lesson_id` FK (a question's content is tied to one lesson permanently)
- `quizzes` — NOT directly linked to lessons/questions by FK. Connected to `questions` via a `quiz_question` pivot table (many-to-many), so the same question can appear in multiple quizzes (e.g. a lesson's summary quiz and a later mixed review quiz) without duplication.

## Known gotchas on this machine

- Windows + antivirus/indexer can lock files mid `composer install`/`update` (usually inside `vendor/laravel/framework`). Project folder is excluded from Defender and Search Indexing to prevent this — if it recurs, check those exclusions are still active.
- CI (`.github/workflows/ci.yml`) pins a specific PHP version — if `composer update` locally bumps package requirements to a newer PHP minimum, update the workflow's `php-version` to match before pushing, or CI will fail on install.

## Not yet deployed

Running locally only for now (no Railway/cloud deploy yet). Revisit once ready to wire up the scheduled content-generation job (Laravel scheduler + queue, calling an LLM to draft quiz questions from existing lesson content for manual review before publishing).