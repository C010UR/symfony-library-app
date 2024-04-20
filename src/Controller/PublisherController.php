<?php

namespace App\Controller;

use App\Controller\Interface\CrudControllerInterface;
use App\Service\ControllerService\PublisherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v1/publishers', name: 'app_api_publisher_', format: 'json')]
class PublisherController extends AbstractController implements CrudControllerInterface
{
    public function __construct(private readonly PublisherService $service)
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

    #[Route('/{slug}', name: 'read_one', methods: ['GET'])]
    public function readOne(string $slug): JsonResponse
    {
        return new JsonResponse($this->service->findWithSlug($slug)->format());
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return new JsonResponse($this->service->create($request));
    }

    #[Route('/{id}', name: 'update', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return new JsonResponse($this->service->update($request, $id));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
