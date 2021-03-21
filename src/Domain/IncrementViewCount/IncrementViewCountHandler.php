<?php

declare(strict_types=1);

namespace App\Domain\IncrementViewCount;

use App\Domain\Exceptions\ShortenedUrlDoesNotExistException;
use App\Domain\ShortenedUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class IncrementViewCountHandler implements MessageHandlerInterface
{
    private ShortenedUrlRepository $repository;

    public function __construct(ShortenedUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(IncrementViewCountCommand $command): void
    {
        $url = $this->repository->getShortenedUrlByUri($command->getUri());

        if (!$url) {
            throw new ShortenedUrlDoesNotExistException();
        }

        $url->incrementCount();
        $url->record(new ViewCountIncrementedEvent());
        $this->repository->save($url);
    }
}
