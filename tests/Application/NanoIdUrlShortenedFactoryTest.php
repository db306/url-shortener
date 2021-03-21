<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\NanoIdUrlShortenerFactory;
use App\Shared\Application\Identity\IdGenerator;
use App\Shared\Application\Identity\UriGenerator;
use PHPUnit\Framework\TestCase;

class NanoIdUrlShortenedFactoryTest extends TestCase
{
    public function testGenerationShouldInvokeGenerators()
    {
        $idGenerator = $this->getMockBuilder(IdGenerator::class)->getMock();
        $idGenerator->expects($this->once())->method('generate')->willReturn('id');
        $uriGenerator = $this->getMockBuilder(UriGenerator::class)->getMock();
        $uriGenerator->expects($this->once())->method('generate')->willReturn('uri');

        $factory = $this->getMockBuilder(NanoIdUrlShortenerFactory::class)->setConstructorArgs([
            $idGenerator,
            $uriGenerator,
        ])->getMockForAbstractClass();

        $output = $factory->generate('test');

        $this->assertEquals('id', $output->getId());
        $this->assertEquals('uri', $output->getUri());
        $this->assertEquals('test', $output->getRedirectUrl());
    }
}
