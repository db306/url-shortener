<?php

declare(strict_types=1);

namespace App\Tests\AddShortenedUrl;

use App\Domain\AddShortenedUrl\AddShortenedUrlCommand;
use App\Domain\AddShortenedUrl\AddShortenedUrlEvent;
use App\Domain\AddShortenedUrl\AddShortenedUrlHandler;
use App\Domain\ShortenedUrl;
use App\Domain\ShortenedUrlRepository;
use App\Domain\UrlShortenerFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddShortenedUrlTest extends TestCase
{
    private MockObject $repository;
    private MockObject $factory;

    public function setUp(): void
    {
        $this->repository = $this->getMockBuilder(ShortenedUrlRepository::class)->getMock();
        $this->factory = $this->getMockBuilder(UrlShortenerFactory::class)->getMock();
    }

    public function testAddShortenedUrlShouldCallFactoryService()
    {
        $command = new AddShortenedUrlCommand('url');

        $this->factory->expects($this->once())->method('generate')->with('url');

        $handler = $this->getMockBuilder(AddShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
            $this->factory,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    public function testAppendEventWhenUrlAdded()
    {
        $command = new AddShortenedUrlCommand('url');
        $mockUrl = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $mockUrl->expects($this->once())->method('record')->with(new AddShortenedUrlEvent());
        $this->factory->method('generate')->willReturn($mockUrl);

        $handler = $this->getMockBuilder(AddShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
            $this->factory,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    public function testShortenedUrlIsSaved()
    {
        $command = new AddShortenedUrlCommand('url');
        $mockUrl = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $this->factory->method('generate')->willReturn($mockUrl);
        $this->repository->expects($this->once())->method('save')->with($mockUrl);

        $handler = $this->getMockBuilder(AddShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
            $this->factory,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    public function testShortenedUrlShouldReturnItsUri()
    {
        $command = new AddShortenedUrlCommand('url');
        $mockUrl = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $mockUrl->expects($this->once())->method('getUri')->willReturn('testUri');
        $this->factory->method('generate')->willReturn($mockUrl);
        $this->repository->expects($this->once())->method('save')->with($mockUrl);

        $handler = $this->getMockBuilder(AddShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
            $this->factory,
        ])->getMockForAbstractClass();

        $returnedValue = $handler($command);

        $this->assertEquals('testUri', $returnedValue);
    }
}
