<?php

namespace App\EntityListener;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Utils\FileUtils;

#[AsEntityListener(event: Events::prePersist, entity: Tag::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Tag::class)]
class TagEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
        private string $dirPublic
    ) {
    }

    public function prePersist(Tag $book, LifecycleEventArgs $event): void
    {
        $book->normalizeName();
        $book->computeSlug($this->slugger);
    }

    public function preUpdate(Tag $book, PreUpdateEventArgs $event): void
    {
        $book->normalizeName();
        $book->computeSlug($this->slugger);
    }
}
