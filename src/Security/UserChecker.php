<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Exception\AccountInactive;
use App\Security\Exception\InvalidCredentials;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        /** @var User $user */
        if ($user->isDeleted()) {
            throw new CustomUserMessageAccountStatusException(InvalidCredentials::MESSAGE);
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException(AccountInactive::MESSAGE);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
