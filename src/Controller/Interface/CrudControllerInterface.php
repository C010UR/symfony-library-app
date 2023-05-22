<?php

namespace App\Controller\Interface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface CrudControllerInterface
{
    /**
     * Returns json of filtered list of entities.
     */
    public function readAll(Request $request): JsonResponse;

    /**
     * Returns json of filtration meta info.
     */
    public function getMeta(): JsonResponse;

    /**
     * Return json of entity with id $id.
     */
    public function readOne(int $id): JsonResponse;

    /**
     * Creates new entity using request body.
     */
    public function create(Request $request): JsonResponse;

    /**
     * Updates entity with id $id using request body.
     */
    public function update(Request $request, int $id): JsonResponse;

    /**
     * Deletes entity with id $id.
     */
    public function delete(int $id): JsonResponse;
}
