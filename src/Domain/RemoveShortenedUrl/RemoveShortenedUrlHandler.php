<?php

declare(strict_types=1);

namespace App\Domain\RemoveShortenedUrl;

use App\Domain\Exceptions\ShortenedUrlDoesNotExistException;
use App\Domain\ShortenedUrlRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveShortenedUrlHandler implements MessageHandlerInterface
{
    private ShortenedUrlRepository $repository;

    public function __construct(ShortenedUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RemoveShortenedUrlCommand $command)
    {
        $url = $this->repository->getShortenedUrlById($command->getId());

        if (!$url) {
            throw new ShortenedUrlDoesNotExistException();
        }

        $url->record(new ShortenedUrlRemovedEvent());
        $this->repository->removeShortenedUrl($url);
    }
}
