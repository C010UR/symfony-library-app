<?php

namespace App\EntityListener;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    #[AsEntityListener(event: Events::prePersist, entity: Book::class)]
    public function prePersist(Book $book, LifecycleEventArgs $event): void
    {
        $book->normalizeName($book);

        $book->computeSlug($this->slugger);
    }

    #[AsEntityListener(event: Events::preUpdate, entity: Book::class)]
    public function preUpdate(Book $book, PreUpdateEventArgs $event): void
    {
        $book->normalizeName($book);

        if ($event->hasChangedField('name')) {
            $book->computeSlug($this->slugger);
        }
    }
}
