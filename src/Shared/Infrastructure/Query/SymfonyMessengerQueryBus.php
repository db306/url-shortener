<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Query;

use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Bus\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyMessengerQueryBus implements QueryBus
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function handleQuery($query)
    {
        return $this->handle($query);
    }
}
