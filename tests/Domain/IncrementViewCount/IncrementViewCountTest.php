<?php

declare(strict_types=1);

namespace App\Tests\IncrementViewCount;

use App\Domain\Exceptions\ShortenedUrlDoesNotExistException;
use App\Domain\IncrementViewCount\IncrementViewCountCommand;
use App\Domain\IncrementViewCount\IncrementViewCountHandler;
use App\Domain\IncrementViewCount\ViewCountIncrementedEvent;
use App\Domain\ShortenedUrl;
use App\Domain\ShortenedUrlRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IncrementViewCountTest extends TestCase
{
    private MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->getMockBuilder(ShortenedUrlRepository::class)->getMock();
    }

    /**
     * Incrementing Unexisting Url should throw an error.
     */
    public function testIncrementingUnexistingUrlShouldThrowAnError()
    {
        $this->expectException(ShortenedUrlDoesNotExistException::class);

        $command = new IncrementViewCountCommand('test');

        $this->repository->expects($this->once())->method('getShortenedUrlByUri')->willReturn(null);

        $handler = $this->getMockBuilder(IncrementViewCountHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    /**
     * Increment count should actually increment the count.
     */
    public function testIncrementCountShouldIncrementCount()
    {
        $urlMock = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $urlMock->expects($this->once())->method('incrementCount');

        $this->repository->method('getShortenedUrlByUri')->willReturn($urlMock);
        $command = new IncrementViewCountCommand('test');

        $handler = $this->getMockBuilder(IncrementViewCountHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    /**
     * Increment count should append an event to be dispatched.
     */
    public function testIncrementCountShouldAppendAnEvent()
    {
        $urlMock = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $urlMock->expects($this->once())->method('record')->with(new ViewCountIncrementedEvent());

        $this->repository->method('getShortenedUrlByUri')->willReturn($urlMock);
        $command = new IncrementViewCountCommand('test');

        $handler = $this->getMockBuilder(IncrementViewCountHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();

        $handler($command);
    }

    /**
     * Save method should be invoked when adding a new url.
     */
    public function testSaveMethodShouldBeInvokedWithNewUrlShortened()
    {
        $urlMock = $this->getMockBuilder(ShortenedUrl::class)->disableOriginalConstructor()->getMock();
        $this->repository->method('getShortenedUrlByUri')->willReturn($urlMock);
        $this->repository->expects($this->once())->method('save');
        $command = new IncrementViewCountCommand('test');

        $handler = $this->getMockBuilder(IncrementViewCountHandler::class)->setConstructorArgs([
            $this->repository,
        ])->getMockForAbstractClass();

        $handler($command);
    }
}
