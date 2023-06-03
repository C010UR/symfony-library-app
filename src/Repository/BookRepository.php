<?php

namespace App\Repository;

use App\Entity\Book;
use App\Repository\Abstract\AbstractCrudRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends AbstractCrudRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
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
            ->leftJoin('c.tags', 'tags')
            ->leftJoin('c.authors', 'authors')
            ->leftJoin('c.publisher', 'publisher');

        return $this->findMatchingByDeletedWithQueryBuilder($query, $isDeleted, $criteria);
    }
}
