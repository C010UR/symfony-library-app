<?php

namespace App\Controller;

use App\Service\ControllerService\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v1/orders', name: 'app_api_orders', format: 'json')]
class OrderController extends AbstractController
{
    public function __construct(private readonly OrderService $service)
    {
    }

    #[Route('', name: 'read_all', methods: ['GET'])]
    public function readAll(Request $request): JsonResponse
    {
        return new JsonResponse($this->service->getAll($request));
    }

    #[Route('/meta', name: 'get_meta', methods: ['GET'])]
    public function getMeta(): JsonResponse
    {
        return new JsonResponse($this->service->getFilterMeta());
    }

    #[Route('/{id}', name: 'read_one', methods: ['GET'])]
    public function readOne(int $id): JsonResponse
    {
        return new JsonResponse($this->service->find($id)->format());
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return new JsonResponse($this->service->create($request));
    }

    #[Route('/{id}', name: 'complete', methods: ['POST'])]
    public function update(int $id): JsonResponse
    {
        return new JsonResponse($this->service->complete($id));
    }
}
