<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    #[AsEventListener(event: LogoutEvent::class, dispatcher: 'security.event_dispatcher.main')]
    public function onLogout(LogoutEvent $logoutEvent): void
    {
        $logoutEvent->setResponse(new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT));
    }
}
