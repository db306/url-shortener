<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Event;

use App\Shared\Application\Event\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyEventDispatcher implements EventDispatcher
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function dispatch($event): void
    {
        $this->eventBus->dispatch($event);
    }
}
