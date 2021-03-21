<?php

declare(strict_types=1);

namespace App\Domain;

interface UrlShortenerFactory
{
    public function generate(string $url): ShortenedUrl;
}
