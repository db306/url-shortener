<?php

declare(strict_types=1);

namespace App\Domain;

interface ShortenedUrlRepository
{
    public function save(ShortenedUrl $url): void;

    public function getShortenedUrlByUri(string $uri): ?ShortenedUrl;

    public function getShortenedUrlById(string $id): ?ShortenedUrl;

    public function getUrlCount(string $id): ?int;

    public function getShortenedUrls(): array;

    public function removeShortenedUrl(ShortenedUrl $shortenedUrl): void;
}
