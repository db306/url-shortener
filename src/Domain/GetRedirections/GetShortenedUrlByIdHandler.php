<?php

declare(strict_types=1);

namespace App\Domain\GetRedirections;

use App\Domain\ShortenedUrlDto;
use App\Domain\ShortenedUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetShortenedUrlByIdHandler implements MessageHandlerInterface
{
    private ShortenedUrlRepository $repository;

    public function __construct(ShortenedUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetShortenedUrlByIdQuery $query): ?ShortenedUrlDto
    {
        $urlShortened = $this->repository->getShortenedUrlById($query->getId());

        return $urlShortened ? ShortenedUrlDto::fromAggregate($urlShortened) : null;
    }
}
