<?php

namespace App\EntityListener;

use App\Entity\User;
use App\Utils\FileUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
#[AsEntityListener(event: Events::postRemove, entity: User::class)]
class UserEntityListener
{
    public function __construct(private SluggerInterface $slugger, private string $dirPublic)
    {
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        $user->normalizeName();
        $user->computeSlug($this->slugger);
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $user->normalizeName();
        $user->computeSlug($this->slugger);

        if ($user->isDeleted()) {
            $user->setIsActive(false);
            $event->getObjectManager()->persist($user);
        }

        if ($event->hasChangedField('imagePath')) {
            FileUtils::unlinkUpload($this->dirPublic, $event->getOldValue('imagePath'));
        }
    }

    public function postRemove(User $user): void
    {
        FileUtils::unlinkUpload($this->dirPublic, $user->getImagePath());
    }
}
