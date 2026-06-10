# Exercise 1 — FizzBuzz

A small, extensible FizzBuzz **rule engine** in PHP 8.5.

The transformation is expressed as composable `Rule` objects resolved by a `RuleCollection`,
rather than a hard-coded conditional chain.
The design rationale (total function, rule engine, composite, injection-order priority) lives in [`DECISIONS.md`](DECISIONS.md).

## Requirements

- PHP 8.5+
- [Composer](https://getcomposer.org/)

Everything below can also run through the shared Docker container (see the root `README`).
Prefix any command with `docker compose exec -w /app/Algo app …` from the repository root.

## Install

```bash
composer install
```

## Run

Print the FizzBuzz sequence for 1..100:

```bash
composer fizzbuzz
# or directly
php bin/fizzbuzz.php
```

The iteration range is the caller's concern: `bin/fizzbuzz.php` owns the loop,
while `FizzBuzz::evaluate(int $n): string` stays a pure, total function.

## Quality & tests

```bash
composer test      # PHPUnit
composer phpstan   # PHPStan, level 8
composer cs-check  # PHP-CS-Fixer (PSR-12), dry-run
composer cs-fix    # PHP-CS-Fixer, apply fixes
composer quality   # cs-check + phpstan + test
```
