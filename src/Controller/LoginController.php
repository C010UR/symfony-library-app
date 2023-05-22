<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', name: 'app_api_', format: 'json')]
class LoginController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return new JsonResponse(User::format($this->getUser()));
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): void
    {
    }

    #[Route('/profile', name: 'profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        return new JsonResponse(User::format($this->getUser()));
    }
}
