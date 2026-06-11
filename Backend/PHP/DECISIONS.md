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