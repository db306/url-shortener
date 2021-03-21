<?php

declare(strict_types=1);

namespace App\Domain;

use App\Shared\Domain\EventRecorder\ContainsEventsInterface;
use App\Shared\Domain\EventRecorder\PrivateEventRecorderTrait;
use App\Shared\Domain\EventRecorder\RecordsEventsInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ShortenedUrl implements ContainsEventsInterface, RecordsEventsInterface
{
    use PrivateEventRecorderTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $redirectUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $uri;

    /**
     * @ORM\Column(type="integer")
     */
    private int $count;

    public function __construct(
        string $id,
        string $redirectUrl,
        string $uri
    ) {
        $this->id = $id;
        $this->redirectUrl = $redirectUrl;
        $this->uri = $uri;
        $this->count = $count = 0;
    }

    public function incrementCount(): void
    {
        ++$this->count;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
