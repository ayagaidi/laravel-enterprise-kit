# Architecture

## Goals

Laravel Enterprise Kit favors explicit boundaries over magic. The foundation separates identity, authorization, traceability, configuration and API concerns so that business modules can be added without rewriting security primitives.

## Layers

1. **HTTP** — controllers, form requests and middleware validate inputs and enforce route-level access.
2. **Domain models** — users, roles, permissions, settings and audit events own persistence relationships.
3. **Services** — cross-cutting behavior such as auditing and cached settings lives outside controllers.
4. **API boundary** — `/api/v1` is versioned independently from the Blade admin interface.
5. **Quality gate** — feature tests and Pint run on pull requests and `main`.

## RBAC

Users receive roles; roles receive permissions. Permission slugs are stable integration identifiers. `super-admin` is intentionally exceptional and bypasses normal permission lookup.

## Audit trail

Audit events are append-only application records. The service redacts common secret fields and records request metadata. Production deployments should additionally define retention, archival and access-control policies appropriate to their compliance requirements.

## Settings

System settings have a key, value, type, group and public/private flag. Reads are cached. Writes invalidate the corresponding cache key. Public API exposure is opt-in.

## Security boundaries

- authentication does not imply authorization;
- disabled accounts are rejected after credential verification;
- sensitive audit values are redacted;
- demo administrator creation is environment-controlled;
- API authentication uses Sanctum tokens;
- public settings must be explicitly marked public.

## Deliberate non-goals in v0.1

Multi-tenancy, document workflows, approval chains, queues, SSO and observability are not included yet. They are staged in the roadmap to keep the first release reviewable and trustworthy.
