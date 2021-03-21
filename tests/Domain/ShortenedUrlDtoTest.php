<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\ShortenedUrl;
use App\Domain\ShortenedUrlDto;
use PHPUnit\Framework\TestCase;

class ShortenedUrlDtoTest extends TestCase
{
    /**
     * test creating a new dto should hydrate properties.
     */
    public function testCreatingObjectShouldHydrateProps()
    {
        $url = new ShortenedUrlDto(
            'id',
            'redirect',
            'uri',
            3
        );

        $this->assertEquals('id', $url->getId());
        $this->assertEquals('redirect', $url->getRedirectUrl());
        $this->assertEquals('uri', $url->getUri());
        $this->assertEquals(3, $url->getCount());
    }

    /**
     * test static factory generation should create new dto.
     */
    public function testStaticFactoryShouldHydrateDto()
    {
        $shortenedUrl = new ShortenedUrl(
            'id',
            'redirect',
            'uri'
        );

        $url = ShortenedUrlDto::fromAggregate($shortenedUrl);

        $this->assertEquals('id', $url->getId());
        $this->assertEquals('redirect', $url->getRedirectUrl());
        $this->assertEquals('uri', $url->getUri());
        $this->assertEquals(0, $url->getCount());
    }
}
