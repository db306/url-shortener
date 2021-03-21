<?php

declare(strict_types=1);

namespace App\Domain\IncrementViewCount;

class IncrementViewCountCommand
{
    private string $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
