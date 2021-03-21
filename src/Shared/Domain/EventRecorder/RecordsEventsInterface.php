<?php

declare(strict_types=1);

namespace App\Shared\Domain\EventRecorder;

interface RecordsEventsInterface
{
    public function record($event): void;
}
