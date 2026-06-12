# Decisions — Exercise 2 (Fleet)

Exercise-specific decisions in lightweight ADR form. Cross-cutting decisions (one Composer project per
exercise, shared Docker image, quality tooling) live in the root `DECISIONS.md`.

## 1. Promote the boilerplate, keep the original as a snapshot

**Context:** `Backend/PHP/Boilerplate/` holds the exercise's starting point.

**Decision:** The working project lives in `Backend/PHP/`.
`Boilerplate/` is left untouched as a frozen reference. Deleting it would only add noise to the diff.

## 2. Behat step definitions use PHP 8 attributes

**Context:** The boilerplate context stored results in dynamic properties (`$this->$var`), deprecated
since PHP 8.2. Under PHP 8.5 the deprecation surfaces and Behat fails the scenario.

**Decision:** Step definitions use `#[Given/When/Then]` attributes over typed state, no docblock annotations.

## 3. Fleet is the aggregate root and Vehicle is a local entity

**Context:** A vehicle can belong to several fleets, and every invariant (no duplicate plate in a fleet,
no same location twice in a row) is scoped to a single fleet. There is no global, cross-fleet vehicle rule.

**Decision:** `Fleet` is the aggregate root and consistency boundary.
`Vehicle` is an entity inside a `Fleet`, identified by its `PlateNumber`.
The root owns its entities' lifecycle: `Fleet::register(PlateNumber)` builds
the `Vehicle` itself instead of accepting one, so no external mutable reference can bypass the root.

## 4. Package layout: per-use-case in App, by stereotype in Domain

**Context:** A flat layout turns heterogeneous as soon as a few use cases grow their own folders.

**Decision:** `App/` groups each use case: `Command/CreateFleet/{CreateFleetCommand, CreateFleetCommandHandler}`.
`Domain/` is split by DDD stereotype: `Aggregate/`, `Entity/`, `ValueObject/`, `Repository/`, `Exception/`.
Adopted from the start for homogeneity, and to encode each component's role in the tree (including the Fleet/Vehicle distinction).

## 5. Identifiers are agnostic string value objects

**Context:** UUIDs are minted by the application/infrastructure, and their unpredictability is not a domain concern.

**Decision:** `FleetId`, `PlateNumber` and `UserId` wrap a non-empty string with equality.
No UUID coupling, no `::generate()`.
An empty value throws `\DomainException` (a value outside the valid domain, not a generic argument error).
The UUID v7 is generated at the CLI edge in step 2.

## 6. Queries reveal intent and never return the aggregate

**Context:** Returning `Fleet` from a query would hand the mutable write model to callers, who could then
mutate it outside any command handler.

**Decision:** Each query answers one question (`IsVehicleRegistered` → `bool`). This preserves the read/write
separation and lets step 2 back a query with an efficient read instead of rehydrating the aggregate.

## 7. Handlers are plain classes with `handle()`, no bus

**Context:** The exercise forbids a service container, and a command bus would add indirection for no gain here.

**Decision:** Each command and query has one handler exposing `handle()`, instantiated and called directly by
the CLI and the Behat context.

## 8. Unit tests double the port, not the domain

**Context:** Behat already covers the happy path end to end through the in-memory repository.

**Decision:** Unit tests mock or stub only the `FleetRepository` port, whereas `Fleet` and value objects stay real.
Command handlers use mocks asserting the side effect (`save` once, or never on the error path).
Query handlers use stubs and assert the returned value.
The in-memory repository keeps its own test, since the fake must be trustworthy.