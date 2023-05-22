<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserEntityListener
{
    public function __construct(private string $dirPublic)
    {
    }

    private function unlink(?string $imagePath): bool
    {
        if ($imagePath === null) {
            return true;
        }

        return @unlink(sprintf('%s%s', $this->dirPublic, $imagePath));
    }

    public function postRemove(User $user): void
    {
        $this->unlink($user->getImagePath());
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        if ($user->isDeleted()) {
            $user->setIsActive(false);
            $event->getObjectManager()->persist($user);
        }

        if ($event->hasChangedField('imagePath')) {
            $this->unlink($event->getOldValue('imagePath'));
        }
    }
}
