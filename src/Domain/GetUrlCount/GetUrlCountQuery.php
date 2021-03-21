<?php

declare(strict_types=1);

namespace App\Domain\GetUrlCount;

class GetUrlCountQuery
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
