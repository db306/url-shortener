<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\ShortenedUrl;
use App\Domain\UrlShortenerFactory;
use App\Shared\Application\Identity\IdGenerator;
use App\Shared\Application\Identity\UriGenerator;

class NanoIdUrlShortenerFactory implements UrlShortenerFactory
{
    private IdGenerator $idGenerator;
    private UriGenerator $uriGenerator;

    public function __construct(
        IdGenerator $idGenerator,
        UriGenerator $uriGenerator
    ) {
        $this->idGenerator = $idGenerator;
        $this->uriGenerator = $uriGenerator;
    }

    public function generate(string $url): ShortenedUrl
    {
        return new ShortenedUrl(
            $this->idGenerator->generate(),
            $url,
            $this->uriGenerator->generate()
        );
    }
}
