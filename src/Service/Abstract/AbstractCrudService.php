<?php

namespace App\Service\Abstract;

use App\Entity\Interface\EntityInterface;
use App\Entity\User;
use App\Repository\Abstract\AbstractCrudRepository;
use App\Utils\Filter\Column;
use App\Utils\Filter\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractCrudService
{
    private QueryParser $queryParser;

    private AbstractCrudRepository $repository;

    private Security $security;

    protected function setQueryParser(QueryParser $queryParser): self
    {
        $this->queryParser = $queryParser;

        if ($this->isAdmin()) {
            $queryParser->addColumns(new Column([
                'name' => 'isDeleted',
                'label' => 'Deleted',
                'type' => Column::BOOLEAN_TYPE,
                'isOrderable' => true,
                'isSearchable' => false,
            ]));
        }

        return $this;
    }

    protected function setRepository(AbstractCrudRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    protected function setSecurity(Security $security): self
    {
        $this->security = $security;

        return $this;
    }

    protected function getQueryParser(): ?QueryParser
    {
        return $this->queryParser;
    }

    protected function getRepository(): ?AbstractCrudRepository
    {
        return $this->repository;
    }

    protected function getSecurity(): ?Security
    {
        return $this->security;
    }

    public function getFilterMeta(): array
    {
        return $this->getQueryParser()->getColumns();
    }

    protected function isUser(): bool
    {
        if (!$this->getSecurity()->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return false;
        }

        return in_array(User::ROLE_USER, $this->getSecurity()->getUser()->getRoles());
    }

    protected function isAdmin(): bool
    {
        if (!$this->getSecurity()->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return false;
        }

        return in_array(User::ROLE_ADMIN, $this->getSecurity()->getUser()->getRoles());
    }

    public function getAll(Request $request, mixed $additional = null): array
    {
        $query = HeaderUtils::parseQuery($request->getQueryString() ?? '');

        return $this->getRepository()->findMatchingByDeleted(
            $this->isAdmin(),
            $this->getQueryParser()->parseQuery($query, true, true),
        );
    }

    public function delete(int $id): void
    {
        $this->getRepository()->remove($this->find($id), true);
    }

    protected function throwNotFound(string $class)
    {
        $class_parts = explode('\\', $class);
        $entity = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', str_replace('Repository', '', end($class_parts))));

        throw new NotFoundHttpException(sprintf('%s was not found.', $entity));
    }

    protected function findWithRepository(ServiceEntityRepository $repository, array $criteria = []): EntityInterface
    {
        $entity = $repository->findOneBy($criteria);

        if (null === $entity || ($entity->isDeleted() && !$this->isAdmin())) {
            $this->throwNotFound($repository::class);
        }

        return $entity;
    }

    public function find(int $id): ?EntityInterface
    {
        return $this->findWithRepository($this->getRepository(), ['id' => $id]);

        // if ($entity->isDeleted() && !$this->isAdmin()) {
        //     $this->throwNotFound($entity::class);
        // }

        // return $entity;
    }

    public function findWithSlug(string $slug): EntityInterface
    {
        return $this->findWithRepository($this->getRepository(), ['slug' => $slug]);

        // if ($entity->isDeleted() && !$this->isAdmin()) {
        //     $this->throwNotFound($entity::class);
        // }

        // return $entity;
    }
}
