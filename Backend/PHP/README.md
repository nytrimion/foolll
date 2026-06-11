# Exercise 2 — Fleet Management

Vehicle fleet parking management, built with DDD and CQRS.

## Requirements

PHP 8.5 and Composer, or Docker.

## Install

```bash
composer install
```

## Quality & tests

```bash
composer quality   # PHP-CS-Fixer (PSR-12), PHPStan level 8, PHPUnit, Behat
composer test      # PHPUnit only
composer behat     # Behat, default suite
```

## Docker

From the repository root:

```bash
docker compose up -d
docker compose exec app sh -c 'cd /app/Backend/PHP && composer quality'
```