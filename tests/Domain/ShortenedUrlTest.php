<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\ShortenedUrl;
use PHPUnit\Framework\TestCase;

class ShortenedUrlTest extends TestCase
{
    /**
     * Creating new shortened url should hydrate properties.
     */
    public function testCreatingNewShortenedUrlShouldHydrateProps()
    {
        $shortenedUrl = new ShortenedUrl(
            'id',
            'redirect',
            'uri'
        );

        $this->assertEquals('id', $shortenedUrl->getId());
        $this->assertEquals('redirect', $shortenedUrl->getRedirectUrl());
        $this->assertEquals('uri', $shortenedUrl->getUri());
        $this->assertEquals(0, $shortenedUrl->getCount());
    }

    /**
     * Incrementing count should work.
     */
    public function testIncrementingCountShouldWork()
    {
        $shortenedUrl = new ShortenedUrl(
            'id',
            'redirect',
            'uri'
        );
        $this->assertEquals(0, $shortenedUrl->getCount());
        $shortenedUrl->incrementCount();
        $this->assertEquals(1, $shortenedUrl->getCount());
    }
}
