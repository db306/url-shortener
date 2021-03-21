<?php

declare(strict_types=1);

namespace App\Domain\AddShortenedUrl;

class AddShortenedUrlCommand
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
