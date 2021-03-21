<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Identity;

use Hidehalo\Nanoid\Client;

class NanoGeneratorServiceFactory
{
    public function __invoke(): Client
    {
        return new Client();
    }
}
