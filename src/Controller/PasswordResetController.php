<?php

namespace App\Controller;

use App\Service\ControllerService\PasswordResetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/password-reset', name: 'app_api_', format: 'json')]
class PasswordResetController extends AbstractController
{
    #[Route('', name: 'password_reset_request', methods: ['POST'])]
    public function request(Request $request, PasswordResetService $service): JsonResponse
    {
        $service->requestResetPassword($request);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/reset/{token}', name: 'password_reset', methods: ['POST'])]
    public function reset(Request $request, string $token = null, PasswordResetService $service): JsonResponse
    {
        $service->resetPassword($request, $token);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
