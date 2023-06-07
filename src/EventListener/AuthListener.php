<?php

namespace App\EventListener;

use App\Entity\User;
use App\Security\Exception\AccountDeactivated;
use App\Security\Exception\AccountInactive;
use App\Security\Exception\InvalidCredentials;
use App\Security\UserChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Throwable;

class AuthListener
{
    /**
     * @var int
     */
    final public const MAX_LOGIN_ATTEMPTS = 3;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[AsEventListener(event: LoginFailureEvent::class)]
    public function onLoginFailure(LoginFailureEvent $event): void
    {
        try {
            /** @var User $user */
            $user = $event->getPassport()->getUser();
        } catch (Throwable) {
            throw new InvalidCredentials();
        }

        if ($user->isDeleted()) {
            throw new InvalidCredentials();
        }

        if (!$user->isActive()) {
            throw new AccountInactive();
        }

        $attempts = $user->getLoginAttempts();
        if ($attempts < self::MAX_LOGIN_ATTEMPTS) {
            $user->setLoginAttempts(++$attempts);
            $this->entityManager->flush();
            throw new InvalidCredentials();
        }

        $user->setLoginAttempts(0);
        $user->setIsActive(false);

        $this->entityManager->flush();
        throw new AccountDeactivated();
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if ($user->isDeleted()) {
            throw new InvalidCredentials();
        }

        $user->setLoginAttempts(0);
        $this->entityManager->flush();
    }

    #[AsEventListener(event: CheckPassportEvent::class)]
    public function onCheckPassport(CheckPassportEvent $event): void
    {
        try {
            /** @var User $user */
            $user = $event->getPassport()->getUser();
        } catch (Throwable) {
            return;
        }

        if (!$user->isActive()) {
            throw new AccountInactive();
        }

        if ($user->isDeleted()) {
            throw new InvalidCredentials();
        }
    }
}
