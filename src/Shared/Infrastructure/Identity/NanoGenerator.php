<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Identity;

use App\Shared\Application\Identity\UriGenerator;
use Hidehalo\Nanoid\Client;

class NanoGenerator implements UriGenerator
{
    private Client $generator;

    public function __construct(
        Client $generator
    ) {
        $this->generator = $generator;
    }

    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        return $this->generator->generateId();
    }
}
