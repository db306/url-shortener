<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Fixtures;

use App\Domain\ShortenedUrl;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ShortenedUrlFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $url1 = new ShortenedUrl(
            'fffdec73-43fc-4535-8f7f-26c828723f65',
            'https://www.google.com',
            'VgzoeynTLJeIYuDPf2Qy1'
        );

        $manager->persist($url1);

        $url2 = new ShortenedUrl(
            'c95fdf85-f4aa-4fab-badf-2cf4f836cae8',
            'https://fr.jobtome.com/',
            'VgzoeynTLJeIYuDPf2Qy2'
        );

        $manager->persist($url2);

        $url3 = new ShortenedUrl(
            '8ea747c2-5dbc-4eda-b8e0-fa76791d9dde',
            'https://en.jobtome.com/',
            'VgzoeynTLJeIYuDPf2Qy3'
        );

        $manager->persist($url3);

        $url4 = new ShortenedUrl(
            '6d2b9340-e738-41e0-a2cb-f5feff456965',
            'https://www.reddit.com',
            'VgzoeynTLJeIYuDPf2Qy4'
        );

        $manager->persist($url4);

        $manager->flush();
    }
}
