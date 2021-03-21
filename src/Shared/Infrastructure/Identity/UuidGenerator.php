<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Identity;

use App\Shared\Application\Identity\IdGenerator;

class UuidGenerator implements IdGenerator
{
    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        return uuid_create(UUID_TYPE_RANDOM);
    }
}
