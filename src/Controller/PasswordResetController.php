<?php

namespace App\Controller;

use App\Service\ControllerService\PasswordResetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/reset-password', name: 'app_api_', format: 'json')]
class PasswordResetController extends AbstractController
{
    #[Route('', name: 'password_reset_request', methods: ['POST'])]
    public function request(Request $request, PasswordResetService $service): JsonResponse
    {
        $service->requestResetPassword($request);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/reset/{token}', name: 'password_reset', methods: ['POST'])]
    public function reset(Request $request, PasswordResetService $service, string $token = null): JsonResponse
    {
        $service->resetPassword($request, $token);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
