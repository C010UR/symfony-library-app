<?php

namespace App\EntityListener;

use App\Entity\Publisher;
use App\Utils\FileUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Publisher::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Publisher::class)]
#[AsEntityListener(event: Events::postRemove, entity: Publisher::class)]
class PublisherEntityListener
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly string $dirPublic
    ) {
    }

    public function prePersist(Publisher $publisher, LifecycleEventArgs $event): void
    {
        $publisher->computeSlug($this->slugger);
    }

    public function preUpdate(Publisher $publisher, PreUpdateEventArgs $event): void
    {
        $publisher->computeSlug($this->slugger);

        if ($event->hasChangedField('imagePath')) {
            FileUtils::unlinkUpload($this->dirPublic, $event->getOldValue('imagePath'));
        }
    }

    public function postRemove(Publisher $publisher): void
    {
        FileUtils::unlinkUpload($this->dirPublic, $publisher->getImagePath());
    }
}
