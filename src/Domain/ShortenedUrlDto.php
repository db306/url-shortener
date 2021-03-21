<?php

declare(strict_types=1);

namespace App\Domain;

class ShortenedUrlDto
{
    private string $id;
    private string $redirectUrl;
    private string $uri;
    private int $count;

    public function __construct(string $id, string $redirectUrl, string $uri, int $count)
    {
        $this->id = $id;
        $this->redirectUrl = $redirectUrl;
        $this->uri = $uri;
        $this->count = $count;
    }

    public static function fromAggregate(ShortenedUrl $shortenedUrl): self
    {
        return new self(
            $shortenedUrl->getId(),
            $shortenedUrl->getRedirectUrl(),
            $shortenedUrl->getUri(),
            $shortenedUrl->getCount()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
