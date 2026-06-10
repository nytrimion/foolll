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

## 2. A rule engine instead of a conditional chain

**Context:** The naive solution is an `if/else` chain.
It works, but the brief scores *scalability* and asks the candidate to justify their choices.
A conditional chain forces a code edit on every new rule and bakes the priority order into control flow.

**Decision:** The transformation is expressed as composable `Rule` objects resolved by a `RuleCollection`:
- `Rule` requires `matches(int $n): bool` and a readable `string $label` property.
- `DivisibleBy` is the only concrete rule needed for FizzBuzz.
- `FizzBuzz::evaluate` delegates to `RuleCollection::matchFirst`, returning the matched rule's
  `label` or `(string) $n` when nothing matches (no divisibility branching of its own).

**Rationale:**
- Open/closed: a new rule is a new class plus one line of wiring, never an edit to the engine.
- Priority becomes data from injection order, not control flow: `matchFirst` returns the *first*
  matching rule, so the most specific rule must be injected first (the `MatchEvery` FizzBuzz rule
  ahead of the individual Fizz and Buzz rules).
  This is the registration-order priority of HTTP middleware or event listeners,
  kept explicit here rather than implicit.
  The canonical order is already pinned by the `15 -> FizzBuzz` test. 
  A dedicated "wrong order" test would assert a non-contract and is deliberately omitted.
- The variadic `Rule ...$rules` constructor gives compile-time-checked, robustly typed wiring.
- `label` is intrinsic state with no argument, so the interface requires it as a property rather than a getter.
  `DivisibleBy` satisfies it with a promoted `readonly` property, so the contract holds without a getter or a phpdoc.
  `matches(int $n)` stays a method because it takes an argument, so the asymmetry is intentional.

## 3. FizzBuzz is a composite rule carrying an explicit label

**Context:** `15` must yield `FizzBuzz`.
The step-2 wiring used `DivisibleBy(15)`, which is correct only because divisibility by 15 happens
to coincide with "divisible by 3 *and* 5". That coincidence does not generalize: a composite such as
"prime *and* even" has no single equivalent divisor.

**Decision:** "matches when every sub-rule matches" is modeled as `MatchEvery`, a `Rule` that composes
a `RuleCollection` and carries an explicit `string $label`.
`RuleCollection::matchEvery` (`array_all`) provides the traversal delegated by `MatchEvery::matches`.
The FizzBuzz rule is a `MatchEvery` of the Fizz and Buzz rules, wired first.

**Rationale:**
- `MatchEvery` reuses the Fizz and Buzz instances the engine already holds (composition, not duplication).
- `matchEvery` mirrors `matchFirst`: both keep rule traversal inside the collection and express intent
  declaratively (`array_all` / `array_find`), rather than scattering loops across rules.
