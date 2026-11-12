# SmartMatch Backend Architecture Refactor

## Intent

Reorganize the Sprint 2 SmartMatch backend into explicit entity, DAO, service,
controller, helper, and API layers while preserving the existing application
behavior. The refactor is intended to make responsibilities clear without
changing the JSON records, frontend design, request formats, response formats,
authentication behavior, or image URLs.

## Goals

- Implement the requested `backend/api`, `classes/entities`, `classes/dao`,
  `classes/services`, `controller`, `conf`, and `helpers` structure.
- Keep JSON file access and file locking exclusively in DAOs.
- Move offer and competence validation into services.
- Keep authentication and public-user shaping in `AuthService`/`UserDAO`.
- Preserve all existing resource routes, methods, status codes, session cookies,
  upload validation, image replacement, and image deletion behavior.
- Keep the existing root `backend/api.php` as a compatibility entry point that
  delegates to the canonical `backend/api/api.php`.

## Non-goals and anti-goals

- No frontend UI, HTML, CSS, or JavaScript redesign.
- No change to JSON field names or stored records.
- No framework, database, PDO, or SQL.
- No removal of legacy entry points until verification is complete.
- No duplicated validation, persistence, or authentication logic.
- No unnecessary abstractions beyond the requested layers.

## Constraints

- Native PHP and OOP only.
- All internal loading uses `require_once` and `__DIR__`.
- Existing data remains under `backend/data/`, including
  `backend/data/uploads/offres/`.
- The frontend currently calls `../backend/api.php` or
  `../../backend/api.php`; this contract must remain valid.

## Approaches

1. **Layered extraction (selected):** extract the existing seams into entities,
   locked JSON DAOs, services, controllers, helpers, and a thin router. This
   matches the requested architecture and has the smallest behavior surface.
2. **Compatibility façade:** add the requested classes around the existing
   procedural implementation. This would reduce movement initially, but would
   leave unclear ownership and risk duplicate pathways.
3. **Full domain rewrite:** redesign the data model around richer domain
   objects. This is unnecessary for the current JSON contract and has the
   highest compatibility risk.

Layered extraction is selected because the current router already exposes
distinct persistence, validation, upload, and authentication operations that
can be moved with minimal semantic change.

## Design

### Entities

`Offre`, `Competence`, and `User` represent one JSON record each. They expose
properties plus getters/setters and conversion to the existing associative
array shape. They do not read files, validate business rules, or inspect HTTP
requests.

### DAOs

`JsonStorage` owns the shared absolute-path JSON read/write implementation and
the current shared/exclusive `flock` behavior. `OffreDAO`, `CompetenceDAO`, and
`UserDAO` provide resource-specific CRUD and lookup operations. DAOs return
entities or arrays as appropriate, but do not perform business validation.

### Services

`OffreService` coordinates offer normalization, validation, recruiter
constraints, persistence, and image lifecycle operations. `CompetenceService`
coordinates normalization, uniqueness validation, and persistence.
`AuthService` handles login verification, session state, logout, session
representation, admin checks, and public user representation. Services receive
their DAOs through constructors and never access JSON files directly.

### Uploads

Upload parsing remains in the request helper/controller boundary, while image
validation, safe MIME/extension detection, storage, replacement, and deletion
move to a dedicated upload helper/service used by `OffreService`. The storage
directory and generated filename behavior remain unchanged.

### Controllers and helpers

Controllers receive services through constructors, interpret request data, and
return structured operation results or throw meaningful exceptions. `Request`
handles method overrides, JSON/form/multipart input, and PUT multipart parsing.
`Response` emits the existing `{success,data}` and `{success,errors}` shapes
with the existing status codes.

### API and compatibility

`backend/api/api.php` initializes the application through
`backend/conf/bootstrap.php`, resolves the resource and ID, applies
authorization rules, delegates to controllers, and catches only at the API
boundary to produce a structured 500 response. The existing
`backend/api.php` delegates to this router so current frontend URLs continue
to work without JavaScript changes.

## Verification

Before removing the old monolithic implementation, verify syntax and exercise
anonymous reads, authentication/session/logout, admin authorization, offer
CRUD with create/update/delete image behavior, competence CRUD, user listing,
JSON and multipart requests, response shapes, status codes, and frontend API
URL compatibility. Preserve all original JSON records and confirm no legacy
references remain before deletion.

## Builder decisions

- Keep the root `backend/api.php` as a compatibility shim rather than changing
  frontend paths.
- Use explicit service/controller result contracts internally while preserving
  the external response envelope.
- Keep request parsing and multipart PUT support in `Request`, not in services.
- Keep upload filesystem operations in a focused helper used by the offer
  service; do not make uploads a general persistence DAO.

## Existing behavior baseline

- `offres`: anonymous `GET` list/detail; admin-only `POST`, `PUT`, and
  `DELETE`. Lists sort by newest `created_at`, then descending ID.
- `competences`: admin-only CRUD; lists sort case-insensitively by `nom`.
- `users`: admin-only `GET`, returning users without `password`.
- `session`: anonymous `GET`, returning `{authenticated,user}`.
- `login`: `POST` with `email` and `password`; only `admin` users may log in.
- `logout`: `POST`, clears the PHP session and cookie.
- Missing/invalid resources and methods retain the current 404, 405, 401, 403,
  404, and 422 outcomes and French error messages.
- Offers require title, domain, years, and a valid recruiter (`recruteur` or
  `admin`); creation requires an image. Competence names are required, limited
  to 80 characters, and unique case-insensitively. Offer title and domain
  limits remain 150 and 100 characters.
- Images accept JPEG, PNG, and WebP content with matching accepted filename
  extensions and a 2 MiB maximum. Replacements delete the prior basename-only
  file after a new image is stored; offer deletion removes its image.
- Runtime verification targets the repository's available PHP 8.x runtime and
  uses syntax checks plus direct HTTP requests through PHP's built-in server,
  with temporary isolated JSON fixtures where mutation tests are needed.
