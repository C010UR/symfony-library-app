<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Repository\Abstract\AbstractCrudRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends AbstractCrudRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function save(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tag $entity, bool $flush = false): void
    {
        $entity->setIsDeleted(!$entity->isDeleted());

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMatchingByDeleted(bool $isDeleted, Criteria $criteria): array
    {
        $query = $this->createQueryBuilder('c');

        return $this->findMatchingByDeletedWithQueryBuilder($query, $isDeleted, $criteria);
    }
}
