<?php

declare(strict_types=1);

namespace App\Shared\Domain\EventRecorder;

trait PrivateEventRecorderTrait
{
    private array $messages = [];

    public function getRecordedEvents(): array
    {
        return $this->messages;
    }

    public function clearRecordedEvents(): void
    {
        $this->messages = [];
    }

    public function record($message): void
    {
        $this->messages[] = $message;
    }
}
