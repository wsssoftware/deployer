<?php

namespace App\Tasks;

use Closure;

readonly class Task
{
    protected string $label;

    public function __construct(
        public Closure|array $closure
    ) {}

    public function isClosure(): bool
    {
        return $this->closure instanceof Closure;
    }

    public function label(string $default): string
    {
        return $this->label ?? $default;
    }

    public function withLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
