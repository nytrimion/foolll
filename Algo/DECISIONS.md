# Decisions — Exercise 1 (FizzBuzz)

Exercise-specific decisions in lightweight ADR form. Cross-cutting decisions
(tooling, Docker, one Composer project per exercise) live in the root `DECISIONS.md`.

## 1. The engine is a total function: range validation belongs to the caller

**Context:** FizzBuzz displays the numbers from 1 to N.
The only untrusted input is N, and this exercise has no CLI reading it.
During iteration, every number handed to the engine is already in `[1, N]` by construction.

**Decision:** `FizzBuzz::evaluate(int $n): string` is a total function defined
over every integer (including `0` and negatives) and performs no input validation.
Producing and bounding the `1..N` range is the caller's responsibility.

**Rationale:**
- Divisibility is mathematically defined for all integers.
  A guard such as `$n >= 1` would couple the engine to a display constraint it has no reason to know.
- The range is a property of the *display*, not of the *transformation*.
  Keeping it out of the engine preserves reusability and keeps `evaluate` a pure, side-effect-free function.
- Validation of N, if ever required, belongs where N enters the system but not in this engine.
- The behavior is pinned by tests (`0`, `-1`, `-3`, `-5`, `-15`), so the choice is explicit rather than accidental.
