<?php

declare(strict_types=1);

namespace App\Domain\GetUrlCount;

use App\Domain\Exceptions\ShortenedUrlDoesNotExistException;
use App\Domain\ShortenedUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetUrlCountHandler implements MessageHandlerInterface
{
    private ShortenedUrlRepository $repository;

    public function __construct(ShortenedUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetUrlCountQuery $query): int
    {
        $count = $this->repository->getUrlCount($query->getId());

        if (is_null($count)) {
            throw new ShortenedUrlDoesNotExistException();
        }

        return $count;
    }
}
