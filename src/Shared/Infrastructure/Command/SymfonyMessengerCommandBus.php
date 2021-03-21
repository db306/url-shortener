<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Command;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Bus\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyMessengerCommandBus implements CommandBus
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function handleCommand($command)
    {
        return $this->handle($command);
    }
}
