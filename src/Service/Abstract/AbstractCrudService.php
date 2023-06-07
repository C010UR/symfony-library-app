<?php

namespace App\Service\Abstract;

use App\Entity\Interface\EntityInterface;
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
        return $this->getQueryParser()->getColumns();
    }

    public function getAll(Request $request, mixed $additional = null): array
    {
        $query = HeaderUtils::parseQuery($request->getQueryString() ?? '');

        return $this->getRepository()->findMatchingByDeleted(
            $query['deleted'] ?? false,
            $this->getQueryParser()->parseQuery($query, true, true),
        );
    }

    public function delete(int $id): void
    {
        $this->getRepository()->remove($this->find($id), true);
    }

    public function findWithRepository(ServiceEntityRepository $repository, array $criteria = []): EntityInterface
    {
        $entity = $repository->findOneBy($criteria);

        if (null === $entity) {
            $class_parts = explode('\\', $repository::class);
            $entity = preg_replace('/(?<!\ )[A-Z]/', ' $0', str_replace('Repository', '', end($class_parts)));

            throw new NotFoundHttpException(sprintf('%s was not found.', $entity));
        }

        return $entity;
    }

    public function find(int $id): EntityInterface
    {
        return $this->findWithRepository($this->getRepository(), ['id' => $id]);
    }

    public function findWithSlug(string $slug): EntityInterface
    {
        return $this->findWithRepository($this->getRepository(), ['slug' => $slug]);
    }
}
