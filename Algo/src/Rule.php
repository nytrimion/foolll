<?php

declare(strict_types=1);

namespace Fulll\Algo;

interface Rule
{
    public string $label { get; }

    public function matches(int $n): bool;
}
