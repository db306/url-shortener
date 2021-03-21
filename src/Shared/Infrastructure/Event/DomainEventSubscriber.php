<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Event;

use App\Shared\Application\Event\EventDispatcher;
use App\Shared\Domain\EventRecorder\ContainsEventsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

class DomainEventSubscriber implements EventSubscriber
{
    private EventDispatcher $eventDispatcher;
    private Collection $entities;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entities = new ArrayCollection();
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
            'preRemove',
            'preFlush',
            'postFlush',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->addContainsEventsEntityToCollection($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->addContainsEventsEntityToCollection($args);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->addContainsEventsEntityToCollection($args);
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $unitOfWork = $args->getEntityManager()->getUnitOfWork();
        foreach ($unitOfWork->getIdentityMap() as $class => $entities) {
            if (!in_array(ContainsEventsInterface::class, class_implements($class), true)) {
                continue;
            }
            foreach ($entities as $entity) {
                $this->entities->add($entity);
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $events = new ArrayCollection();
        foreach ($this->entities as $entity) {
            foreach ($entity->getRecordedEvents() as $domainEvent) {
                $events->add($domainEvent);
            }
            $entity->clearRecordedEvents();
        }

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    private function addContainsEventsEntityToCollection(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof ContainsEventsInterface) {
            $this->entities->add($entity);
        }
    }
}
