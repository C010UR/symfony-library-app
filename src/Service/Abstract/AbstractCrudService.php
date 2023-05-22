<?php

namespace App\Service\Abstract;

use App\Repository\Abstract\AbstractCrudRepository;
use App\Utils\Filter\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractCrudService
{
    private QueryParser $queryParser;
    private AbstractCrudRepository $repository;

    public function setQueryParser(QueryParser $queryParser): self
    {
        $this->queryParser = $queryParser;

        return $this;
    }

    public function setRepository(AbstractCrudRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function getQueryParser(): QueryParser
    {
        return $this->queryParser;
    }

    public function getRepository(): AbstractCrudRepository
    {
        return $this->repository;
    }

    public function getFilterMeta(): array
    {
        return $this->getQueryParser()->getAllowedColumns();
    }

    public function getAll(Request $request, mixed $additional = null): array
    {
        $query = HeaderUtils::parseQuery($request->getQueryString() ?? '');
        return $this->getRepository()->findMatchingByDeleted(
            $query['deleted'] ?? false,
            $this->getQueryParser()->parseQuery($query, true, true),
            $additional,
        );
    }

    public function delete(int $id): void
    {
        $this->getRepository()->remove($this->find($id), true);
    }

    public function findWithRepository(ServiceEntityRepository $repository, array $criteria = []): mixed
    {
        $entity = $repository->findOneBy($criteria);

        if ($entity === null) {
            $class_parts = explode('\\', $repository::class);
            $entity = preg_replace('/(?<!\ )[A-Z]/', ' $0', str_replace('Repository', '', end($class_parts)));

            throw new NotFoundHttpException(sprintf('%s was not found.', $entity));
        }

        return $entity;
    }

    public function find(int $id): mixed
    {
        return $this->findWithRepository($this->getRepository(), ['id' => $id]);
    }
}
