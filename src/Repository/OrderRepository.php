<?php

namespace App\Repository;

use App\Entity\Order;
use App\Repository\Abstract\AbstractCrudRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends AbstractCrudRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMatchingByDeleted(bool $isDeleted, Criteria $criteria): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.book', 'book')
            ->leftJoin('c.userCompleted', 'userCompleted');

        return $this->findMatchingByDeletedWithQueryBuilder($query, $isDeleted, $criteria);
    }
}
