<?php

declare(strict_types=1);

namespace App\Shared\Application\Identity;

interface IdGenerator
{
    public function generate(): string;
}
