<?php

namespace App\Repository;

use App\Entity\Author;
use App\Repository\Abstract\AbstractCrudRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class AuthorRepository extends AbstractCrudRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function save(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Author $entity, bool $flush = false): void
    {
        $entity->setIsDeleted(!$entity->isDeleted());

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMatchingByDeleted(bool $isDeleted, Criteria $criteria): array
    {
        $query = $this->createQueryBuilder('c')
        ->select('c')
        ->innerJoin('c.books', 'books');

        return $this->findMatchingByDeletedWithQueryBuilder($query, $isDeleted, $criteria);
    }
}
