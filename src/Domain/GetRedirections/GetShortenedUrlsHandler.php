<?php

declare(strict_types=1);

namespace App\Domain\GetRedirections;

use App\Domain\ShortenedUrlDto;
use App\Domain\ShortenedUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetShortenedUrlsHandler implements MessageHandlerInterface
{
    private ShortenedUrlRepository $repository;

    public function __construct(ShortenedUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetShortenedUrlsQuery $query)
    {
        $urls = $this->repository->getShortenedUrls();

        return array_map(fn ($url) => ShortenedUrlDto::fromAggregate($url), $urls);
    }
}
