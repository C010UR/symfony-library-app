<?php

namespace App\EntityListener;

use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class PublisherEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    #[AsEntityListener(event: Events::prePersist, entity: Publisher::class)]
    public function prePersist(Publisher $publisher, LifecycleEventArgs $event): void
    {
        $publisher->computeSlug($this->slugger);
    }

    #[AsEntityListener(event: Events::preUpdate, entity: Publisher::class)]
    public function preUpdate(Publisher $publisher, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('name')) {
            $publisher->computeSlug($this->slugger);
        }
    }
}
