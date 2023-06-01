<?php

namespace App\EntityListener;

use App\Entity\User;
use App\Utils\FileUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class UserEntityListener
{
    public function __construct(private string $dirPublic)
    {
    }

    #[AsEntityListener(event: Events::postRemove, entity: User::class)]
    public function postRemove(User $user): void
    {
        FileUtils::unlinkUpload($this->dirPublic, $user->getImagePath());
    }

    #[AsEntityListener(event: Events::preUpdate, entity: User::class)]
    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        if ($user->isDeleted()) {
            $user->setIsActive(false);
            $event->getObjectManager()->persist($user);
        }

        if ($event->hasChangedField('imagePath')) {
            FileUtils::unlinkUpload($this->dirPublic, $event->getOldValue('imagePath'));
        }
    }
}
