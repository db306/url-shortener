<?php

declare(strict_types=1);

namespace App\Tests\API;

use App\Domain\AddShortenedUrl\AddShortenedUrlCommand;
use App\Domain\RemoveShortenedUrl\RemoveShortenedUrlCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UrlShortenedAPIControllerTest extends WebTestCase
{
    public function testGetAllUrls()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls'
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(4, $data);
    }

    public function testAddNewUrlShouldReturnItsUri()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/short-urls',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'url' => 'https://www.laredoute.fr',
            ])
        );

        $created = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue(array_key_exists('uri', $created));
        $this->assertResponseIsSuccessful();
    }

    public function testAddNewUrlShouldShouldReturnAnExtraElement()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/short-urls',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'url' => 'https://www.laredoute.fr',
            ])
        );

        $client->request(
            'GET',
            '/api/short-urls'
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $data);
    }

    public function testAddNewUrlShouldDispatchEvents()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/short-urls',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'url' => 'https://www.laredoute.fr',
            ])
        );

        $commandBus = self::$container->get('messenger.command.bus');
        $commands = $commandBus->getDispatchedMessages();

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(AddShortenedUrlCommand::class, $commands[0]['message']);
        $this->assertEquals('https://www.laredoute.fr', $commands[0]['message']->getUrl());
    }

    public function testAddNewUrlWithWrongDataShouldThrowException()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/short-urls',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'test' => 'test',
            ])
        );

        $output = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseStatusCodeSame(400);

        $commandBus = self::$container->get('messenger.command.bus');
        $commands = $commandBus->getDispatchedMessages();

        $this->assertCount(0, $commands);
        $this->assertEquals('The payload is badly formatted', $output['message']);
    }

    /**
     * @dataProvider dataProviderUrlsToTest
     */
    public function testAddNewUrlWithABadlyFormattedUrl($url, $code)
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/short-urls',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'url' => $url,
            ])
        );

        $this->assertResponseStatusCodeSame($code);
    }

    public function dataProviderUrlsToTest()
    {
        return [
            ['https://google.com', 200],
            ['http://google.com', 200],
            ['https://server.test.great.google.com', 200],
            ['//google.com', 400],
            ['ftp://google.com', 400],
            ['https://§google.com', 400],
        ];
    }

    public function testGetSingleUrl()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff456965'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();

        $this->assertEquals('6d2b9340-e738-41e0-a2cb-f5feff456965', $output['id']);
        $this->assertEquals('https://www.reddit.com', $output['redirectUrl']);
        $this->assertEquals('VgzoeynTLJeIYuDPf2Qy4', $output['uri']);
        $this->assertEquals(0, $output['count']);
    }

    public function testGetSingleUrlNonExistingShouldReturn404()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff45696d'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals('The short Url you provided does not exist', $output['message']);
    }

    public function testGetSingleUrlWithOtherThanUUIDShouldReturn400()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/wedwedwedwed'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertEquals('Id is not a valid UUID', $output['message']);
    }

    public function testDeleteUrlShouldThrowErrorIfNotAUUID()
    {
        $client = static::createClient();

        $client->request(
            'DELETE',
            '/api/short-urls/wedwedwedwed'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertEquals('Id is not a valid UUID', $output['message']);
    }

    public function testDeleteShouldReturn404IfItDoesntExist()
    {
        $client = static::createClient();

        $client->request(
            'DELETE',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff45696d'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals('The url you want to delete, does not exist', $output['message']);
    }

    public function testDeleteShouldReturn200IfCorrectAndReturn3Items()
    {
        $client = static::createClient();

        $client->request(
            'DELETE',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff456965'
        );

        $this->assertResponseIsSuccessful();

        $commandBus = self::$container->get('messenger.command.bus');
        $commands = $commandBus->getDispatchedMessages();

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(RemoveShortenedUrlCommand::class, $commands[0]['message']);
        $this->assertEquals('6d2b9340-e738-41e0-a2cb-f5feff456965', $commands[0]['message']->getId());

        $client->request(
            'GET',
            '/api/short-urls'
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $data);
    }

    public function testCountUrlShouldThrowErrorIfNotAUUID()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/wedwedwedwed/count'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertEquals('Id is not a valid UUID', $output['message']);
    }

    public function testGetLinkCountsShouldReturn404()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff45696d/count'
        );

        $output = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals('The shorten url does not exist', $output['message']);
    }

    public function testGetLinkShouldReturnCount()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff456965/count'
        );

        $this->assertResponseIsSuccessful();

        $output = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(0, $output['count']);
    }
}
