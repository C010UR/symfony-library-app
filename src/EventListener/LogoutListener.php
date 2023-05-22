<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    public function onLogout(LogoutEvent $logoutEvent): void
    {
        $logoutEvent->setResponse(new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT));
    }
}
