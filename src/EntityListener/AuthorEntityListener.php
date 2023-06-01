<?php

namespace App\EntityListener;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    #[AsEntityListener(event: Events::prePersist, entity: Author::class)]
    public function prePersist(Author $author, LifecycleEventArgs $event): void
    {
        $author->normalizeName($author);

        $author->computeSlug($this->slugger);
    }

    #[AsEntityListener(event: Events::preUpdate, entity: Author::class)]
    public function preUpdate(Author $author, PreUpdateEventArgs $event): void
    {
        $author->normalizeName($author);

        if ($event->hasChangedField('firstName') || $event->hasChangedField('lastName') || $event->hasChangedField('middleName')) {
            $author->computeSlug($this->slugger);
        }
    }
}
