<?php

declare(strict_types=1);

namespace App\Domain\GetRedirections;

class GetShortenedUrlByUriQuery
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
