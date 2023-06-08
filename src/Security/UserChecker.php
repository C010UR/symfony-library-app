<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        /** @var User $user */
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('test');
        }

        if ($user->isDeleted()) {
            throw new CustomUserMessageAccountStatusException('test');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
