<?php

declare(strict_types=1);

namespace App\Shared\Application\Identity;

interface UriGenerator
{
    public function generate(): string;
}
