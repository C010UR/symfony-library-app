<?php

namespace App\EventListener;

use App\Entity\User;
use App\Security\UserChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Throwable;

class AuthListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: LoginFailureEvent::class)]
    public function onLoginFailure(LoginFailureEvent $event): void
    {
        try {
            /** @var User $user */
            $user = $event->getPassport()->getUser();
        } catch (Throwable) {
            throw new UnauthorizedHttpException('-', UserChecker::MESSAGE_INVALID_CREDENTIALS);
        }

        if ($user->isDeleted()) {
            throw new UnauthorizedHttpException('-', UserChecker::MESSAGE_INVALID_CREDENTIALS);
        }

        if (!$user->isActive()) {
            throw new UnauthorizedHttpException('-', UserChecker::MESSAGE_ACCOUNT_INACTIVE);
        }

        $attempts = $user->getLoginAttempts();
        if ($attempts < UserChecker::MAX_LOGIN_ATTEMPTS) {
            $user->setLoginAttempts(++$attempts);
            $this->entityManager->flush();
            throw new UnauthorizedHttpException('-', UserChecker::MESSAGE_INVALID_CREDENTIALS);
        }

        $user->setLoginAttempts(0);
        $user->setIsActive(false);
        $this->entityManager->flush();
        throw new UnauthorizedHttpException('-', UserChecker::MESSAGE_TOO_MANY_ATTEMPTS);
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if ($user->isDeleted()) {
            throw new UnauthorizedHttpException('-', UserChecker::MESSAGE_INVALID_CREDENTIALS);
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
            throw new AccessDeniedHttpException(UserChecker::MESSAGE_ACCOUNT_INACTIVE);
        }

        if ($user->isDeleted()) {
            throw new AccessDeniedHttpException(UserChecker::MESSAGE_INVALID_CREDENTIALS);
        }
    }
}
