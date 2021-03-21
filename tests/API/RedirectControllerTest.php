<?php

declare(strict_types=1);

namespace App\Tests\API;

use App\Domain\IncrementViewCount\IncrementViewCountCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RedirectControllerTest extends WebTestCase
{
    public function testLinkShouldReturn404()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/erferferferfref'
        );

        $this->assertResponseStatusCodeSame(404);

        $output = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('The short Url you provided does not exist', $output['message']);
    }

    public function testLinkShouldRedirect()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/VgzoeynTLJeIYuDPf2Qy4'
        );

        $this->assertResponseRedirects('https://www.reddit.com');
        $commandBus = self::$container->get('messenger.command.bus');

        $commands = $commandBus->getDispatchedMessages();
        $this->assertInstanceOf(IncrementViewCountCommand::class, $commands[0]['message']);
        $this->assertCount(1, $commands);
    }

    public function testGetLinkShouldReturnValidCount()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff456965/count'
        );

        $this->assertResponseIsSuccessful();
        $output = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(0, $output['count']);

        $client->request(
            'GET',
            '/VgzoeynTLJeIYuDPf2Qy4'
        );

        $client->request(
            'GET',
            '/api/short-urls/6d2b9340-e738-41e0-a2cb-f5feff456965/count'
        );

        $this->assertResponseIsSuccessful();
        $output = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(1, $output['count']);
    }
}
