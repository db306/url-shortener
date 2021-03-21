<?php

declare(strict_types=1);

namespace App\Domain\RemoveShortenedUrl;

class RemoveShortenedUrlCommand
{
    private string $id;

    /**
     * RemoveShortenedUrlCommand constructor.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
