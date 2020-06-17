<?php

namespace App\EventSubscriber;

use App\Entity\CreateDateTimeInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class DateTimeSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof CreateDateTimeInterface && !$entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTime('now'));
        }
    }
}
