<?php

namespace App\Service\Interface;

use Symfony\Component\HttpFoundation\Request;

interface CrudServiceInterface
{
    public function getFilterMeta(): array;

    public function getAll(Request $request): array;

    public function create(Request $request): array;

    public function update(Request $request, int $id): array;

    public function delete(int $id): void;

    public function find(int $Id): mixed;
}
