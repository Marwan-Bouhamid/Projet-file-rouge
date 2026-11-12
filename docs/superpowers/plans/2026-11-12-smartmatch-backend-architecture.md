# SmartMatch Backend Architecture Refactor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor the SmartMatch native PHP backend into entities, locked JSON DAOs, services, controllers, helpers, and a canonical API router without changing the existing frontend contract or JSON data.

**Architecture:** Keep `backend/api.php` as a compatibility shim to `backend/api/api.php`. Bootstrap constructs DAOs, services, controllers, and helpers; controllers translate requests into service calls; services own validation, authentication, and upload coordination; DAOs exclusively own JSON persistence and locking.

**Tech Stack:** Native PHP 8.x, PHP sessions, JSON files, `flock`, PHP built-in web server, existing browser Fetch/FormData clients.

**Spec:** [docs/superpowers/specs/2026-11-12-smartmatch-backend-architecture-design.md](../specs/2026-11-12-smartmatch-backend-architecture-design.md)

## Global Constraints

- Work only on the target repository's `Session-2` branch.
- Preserve all existing application functionality and JSON records.
- Do not change frontend UI, HTML, CSS, or JavaScript design.
- Do not introduce Laravel, Symfony, another framework, MySQL, or PDO.
- Use `require_once` and `__DIR__` for internal loading.
- Keep JSON file reading/writing exclusively in DAOs.
- Keep uploads under `backend/data/uploads/offres/`.
- Preserve `{ "success": true, "data": ... }` and `{ "success": false, "errors": [...] }`.
- Do not remove legacy files until all verification passes.

## Review Focus

- Anonymous `GET offres` must remain public while write methods remain admin-only; pin in API integration tests.
- Multipart `PUT offres` must parse fields and image replacement correctly; pin in request/upload tests.
- Failed image validation or storage must not delete the previous image; pin in offer service tests.
- Login/session/logout must never expose passwords and must preserve session cookies; pin in auth integration tests.
- Missing IDs, invalid methods, malformed JSON, and missing records must retain current status codes/envelopes; pin in API integration tests.

---

### Task 1: Add entity models and locked JSON persistence

**Files:**
- Create: `backend/classes/entities/Offre.php`
- Create: `backend/classes/entities/Competence.php`
- Create: `backend/classes/entities/User.php`
- Create: `backend/classes/dao/JsonStorage.php`
- Create: `backend/classes/dao/OffreDAO.php`
- Create: `backend/classes/dao/CompetenceDAO.php`
- Create: `backend/classes/dao/UserDAO.php`
- Test: temporary CLI fixture script under the session workspace (not committed)

**Interfaces:**
- `JsonStorage::__construct(string $file): void`, `all(): array`, `find(int $id): ?array`, `create(array $data): array`, `update(int $id, array $data): ?array`, `delete(int $id): bool`.
- Resource DAOs expose resource CRUD plus `UserDAO::findByEmail(string $email): ?array`, `UserDAO::verifyPassword(string $email, string $password): ?array`, and `UserDAO::publicView(array $user): array`.
- Entities expose constructors from associative records, getters/setters for existing fields, and `toArray(): array`; no filesystem or HTTP calls.

- [ ] **Step 1: Write a fixture check** that creates a temporary JSON file, verifies empty initialization, next-ID assignment, merge updates, missing-ID behavior, deletion, and preservation of records.
- [ ] **Step 2: Run the fixture check** with `php`; expect it to fail because the new classes do not exist.
- [ ] **Step 3: Implement the three entities** using the current JSON field names and nullable values without adding validation or persistence behavior.
- [ ] **Step 4: Implement `JsonStorage`** with absolute-path initialization, shared read locks, exclusive write locks, pretty Unicode JSON encoding, and the current max-ID-plus-one behavior; implement the three resource DAOs as focused wrappers.
- [ ] **Step 5: Run the fixture check** and `php -l` on all new PHP files; expect all checks to pass and the repository JSON files to remain byte-for-byte unchanged.
- [ ] **Step 6: Commit** with `refactor: extract entities and json daos`.

### Task 2: Extract upload helper and business services

**Files:**
- Create: `backend/helpers/ImageUpload.php`
- Create: `backend/classes/services/OffreService.php`
- Create: `backend/classes/services/CompetenceService.php`
- Create: `backend/classes/services/AuthService.php`
- Test: temporary CLI service fixture script under the session workspace (not committed)

**Interfaces:**
- `ImageUpload::validate(array $file): array`, `store(array $file): ?string`, and `remove(?string $image): void`.
- `OffreService` constructor accepts `OffreDAO`, `UserDAO`, and `ImageUpload`; exposes `list(): array`, `find(int $id): ?array`, `create(array $data, ?array $file): array`, `update(int $id, array $data, ?array $file): array`, and `delete(int $id): bool`.
- `CompetenceService` constructor accepts `CompetenceDAO`; exposes `list(): array`, `find(int $id): ?array`, `create(array $data): array`, `update(int $id, array $data): array`, and `delete(int $id): bool`.
- `AuthService` constructor accepts `UserDAO`; exposes `login(string $email, string $password): array`, `session(): array`, `logout(): void`, `isAdmin(): bool`, and `users(): array`.
- Services throw specific `InvalidArgumentException`/`RuntimeException`/`DomainException` messages or an equivalent project-local exception type; controllers will map them to current status codes.

- [ ] **Step 1: Write service fixture checks** for current offer limits/recruiter roles, mandatory create image, competence case-insensitive uniqueness, password verification/public views, and upload MIME/extension/2 MiB rules.
- [ ] **Step 2: Run the fixture checks** and expect failure before implementation.
- [ ] **Step 3: Implement `ImageUpload`** by moving the existing image content detection, size/extension validation, unique filename generation, safe storage fallback, basename deletion, and upload directory behavior without changing accepted formats or messages.
- [ ] **Step 4: Implement `OffreService` and `CompetenceService`** by moving normalization, validation, sorting, CRUD coordination, and image replacement ordering out of the router; ensure a new image is stored before the old one is removed and persistence failures are explicit.
- [ ] **Step 5: Implement `AuthService`** with the existing admin-only login rule, session keys, public session shape, logout cookie destruction, and password removal.
- [ ] **Step 6: Run fixtures and `php -l`**; expect all service checks to pass and no direct JSON file calls to exist in services.
- [ ] **Step 7: Commit** with `refactor: move smartmatch rules into services`.

### Task 3: Add request/response helpers, controllers, and bootstrap

**Files:**
- Create: `backend/helpers/Request.php`
- Create: `backend/helpers/Response.php`
- Create: `backend/controller/OffreController.php`
- Create: `backend/controller/CompetenceController.php`
- Create: `backend/controller/AuthController.php`
- Create: `backend/conf/bootstrap.php`
- Test: temporary helper/controller fixture script under the session workspace (not committed)

**Interfaces:**
- `Request::method(): string`, `Request::body(): array`, `Request::parseMultipartPut(): void`, and `Request::files(): array`.
- `Response::success($data, int $status = 200): never`, `Response::error(array $errors, int $status = 400): never`, plus reusable non-exiting payload methods if needed by controllers.
- Controllers accept their service through constructors and expose resource operations that return structured `['data' => ..., 'status' => ...]` results or throw mapped domain exceptions.
- `bootstrap.php` starts the session, sets JSON headers, defines absolute data/upload paths, loads all dependencies, constructs DAOs/services/controllers, and returns a dependency container/array.

- [ ] **Step 1: Write helper fixture checks** for JSON bodies, form bodies, multipart PUT extraction, method overrides, and exact response envelopes.
- [ ] **Step 2: Implement `Request`** by moving the router's content-type detection and multipart PUT parser; ensure temporary files are created and cleaned up safely.
- [ ] **Step 3: Implement `Response`** with the current JSON flags and status handling, without embedding routing or business rules.
- [ ] **Step 4: Implement controllers** so they only receive request data, call services, enforce no business validation, and translate missing records/invalid arguments/auth failures into the existing status/error shapes.
- [ ] **Step 5: Implement `bootstrap.php`** with `__DIR__`-based paths and constructor injection for every controller/service/DAO.
- [ ] **Step 6: Run fixture checks and `php -l`** on all new files; expect helper behavior to match the old router.
- [ ] **Step 7: Commit** with `refactor: add http helpers and controllers`.

### Task 4: Replace the router and preserve the root API entry point

**Files:**
- Create: `backend/api/api.php`
- Modify: `backend/api.php`
- Test: temporary API integration script under the session workspace (not committed)

**Interfaces:**
- `backend/api/api.php` remains the canonical endpoint using `?resource=` and optional `id`.
- `backend/api.php` remains callable at the existing frontend-relative URL and delegates to the canonical router using an absolute `require_once`.

- [ ] **Step 1: Write an integration matrix** covering anonymous/public reads, admin writes, login/logout/session, users, unknown resources, missing IDs, invalid methods, JSON requests, and multipart create/update.
- [ ] **Step 2: Implement the canonical router** with bootstrap loading, current method/resource/ID resolution, PUT multipart parsing, resource authorization rules, controller delegation, and one API-boundary exception mapper.
- [ ] **Step 3: Replace the root API file** with a thin compatibility delegate; do not modify frontend JavaScript.
- [ ] **Step 4: Run `php -l`** over every backend PHP file and verify no router path depends on the current working directory.
- [ ] **Step 5: Commit** with `refactor: route smartmatch api through layered backend`.

### Task 5: Verify all behavior through the real HTTP entry point

**Files:**
- Modify: none initially; only fix files from Tasks 1-4 if verification finds a refactor regression.
- Test: temporary integration fixtures/scripts under the session workspace (not committed)

- [ ] **Step 1: Start PHP's built-in server** from the repository root with the backend data paths intact.
- [ ] **Step 2: Verify anonymous requests** for `GET offres`, `GET offres&id`, `GET competences`, `GET session`, protected `GET users`, and protected competence access; record actual status codes and JSON envelopes.
- [ ] **Step 3: Verify authentication** with valid admin login, invalid credentials, non-admin credentials, session verification, protected user listing, and logout; confirm no password appears in any response.
- [ ] **Step 4: Verify offer CRUD** using JSON update, multipart create, multipart PUT replacement, invalid image, >2 MiB image, missing recruiter, and delete; confirm JSON records and image files are correct after each case.
- [ ] **Step 5: Verify competence CRUD** including duplicate case variants, update, delete, sorting, and authorization.
- [ ] **Step 6: Verify frontend compatibility** by checking the unchanged frontend API URL calculations and issuing requests through the same root `backend/api.php` path with same-origin cookies.
- [ ] **Step 7: Stop the server**, remove only named temporary fixtures/files, and run a final `git diff --check`, `php -l`, and JSON decode check.

### Task 6: Remove legacy monolith only after verification

**Files:**
- Delete: `backend/class.php`
- Modify: documentation/design if final paths or verification notes need correction

- [ ] **Step 1: Search all tracked files** for `class.php`, direct legacy class names, and old handler function references; expect no live references.
- [ ] **Step 2: Delete `backend/class.php`** only after Task 5 passes.
- [ ] **Step 3: Run the full syntax and HTTP verification matrix again**; expect identical external behavior.
- [ ] **Step 4: Commit** with `refactor: remove legacy backend monolith`.
