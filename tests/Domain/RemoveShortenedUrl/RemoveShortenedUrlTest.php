<?php

declare(strict_types=1);

namespace App\Tests\RemoveShortenedUrl;

use App\Domain\Exceptions\ShortenedUrlDoesNotExistException;
use App\Domain\RemoveShortenedUrl\RemoveShortenedUrlCommand;
use App\Domain\RemoveShortenedUrl\RemoveShortenedUrlHandler;
use App\Domain\RemoveShortenedUrl\ShortenedUrlRemovedEvent;
use App\Domain\ShortenedUrl;
use App\Domain\ShortenedUrlRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RemoveShortenedUrlTest extends TestCase
{
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->getMockBuilder(ShortenedUrlRepository::class)->getMock();
    }

    /**
     * Removing an URL that doesn't exist should throw an exception.
     */
    public function testRemovingUnexistingURlShouldThrowException()
    {
        $this->expectException(ShortenedUrlDoesNotExistException::class);
        $this->repository->expects($this->once())->method('getShortenedUrlById')->willReturn(null);

        $command = new RemoveShortenedUrlCommand('test');
        $handler = $this->getMockBuilder(RemoveShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    public function testRemovingUrlShouldAppendEvent()
    {
        $urlMock = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $urlMock->expects($this->once())->method('record')->with(new ShortenedUrlRemovedEvent());
        $this->repository->method('getShortenedUrlById')->willReturn($urlMock);
        $command = new RemoveShortenedUrlCommand('test');
        $handler = $this->getMockBuilder(RemoveShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();
        $handler($command);
    }

    public function testRemovingUrlShouldInvokeRemoveMethod()
    {
        $urlMock = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $this->repository->method('getShortenedUrlById')->willReturn($urlMock);
        $this->repository->expects($this->once())->method('removeShortenedUrl')->with($urlMock);

        $command = new RemoveShortenedUrlCommand('test');
        $handler = $this->getMockBuilder(RemoveShortenedUrlHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();

        $handler($command);
    }
}
