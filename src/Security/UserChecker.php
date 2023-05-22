<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public const MAX_LOGIN_ATTEMPTS = 3;

    public const MESSAGE_ACCOUNT_INACTIVE = 'Account is not active. Please reset the password to reactive account.';

    public const MESSAGE_TOO_MANY_ATTEMPTS = 'Too many attempts. Account was deactivated. Please reset the password.';

    public const MESSAGE_INVALID_CREDENTIALS = 'Email and/or Password are incorrect. Please try again.';

    public function checkPreAuth(UserInterface $user): void
    {
        /** @var User $user */
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException(self::MESSAGE_ACCOUNT_INACTIVE);
        }

        if ($user->isDeleted()) {
            throw new CustomUserMessageAccountStatusException(self::MESSAGE_INVALID_CREDENTIALS);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
