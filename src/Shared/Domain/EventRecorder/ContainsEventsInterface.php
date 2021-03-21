<?php

declare(strict_types=1);

namespace App\Shared\Domain\EventRecorder;

interface ContainsEventsInterface
{
    public function getRecordedEvents(): array;

    public function clearRecordedEvents(): void;
}
