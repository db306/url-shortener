<?php

declare(strict_types=1);

namespace App\Domain\AddShortenedUrl;

use App\Domain\ShortenedUrlRepository;
use App\Domain\UrlShortenerFactory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddShortenedUrlHandler implements MessageHandlerInterface
{
    private ShortenedUrlRepository $repository;
    private UrlShortenerFactory $factory;

    public function __construct(
        ShortenedUrlRepository $repository,
        UrlShortenerFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function __invoke(AddShortenedUrlCommand $command): string
    {
        $shortenedUrl = $this->factory->generate($command->getUrl());

        $shortenedUrl->record(new AddShortenedUrlEvent());

        $this->repository->save($shortenedUrl);

        return $shortenedUrl->getUri();
    }
}
