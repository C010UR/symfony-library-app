<?php

namespace App\Repository\Abstract;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

abstract class AbstractCrudRepository extends ServiceEntityRepository
{
    final protected function countByQueryBuilder(QueryBuilder $queryBuilder): int
    {
        return $queryBuilder
            ->select(sprintf('COUNT(DISTINCT(c))', $queryBuilder->getRootAliases()[0]))
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();
    }

    abstract public function findMatchingByDeleted(bool $isDeleted, Criteria $criteria): array;

    final protected function findMatchingByDeletedWithQueryBuilder(
        QueryBuilder $query,
        bool $isDeleted,
        Criteria $criteria,
    ): array {
        $query->addCriteria($criteria)->addOrderBy('c.isDeleted', 'ASC');

        if (!$isDeleted) {
            $query->andWhere('c.isDeleted = :isDeleted')->setParameter('isDeleted', false);
        }

        $result = [
            'meta' => [],
            'data' => [],
        ];

        $count = $this->countByQueryBuilder(clone $query);
        $offset = $criteria->getFirstResult() ?? 0;

        if ($criteria->getMaxResults()) {
            $result['meta'] = [
                'paginated' => true,
                'pageSize' => $criteria->getMaxResults(),
                'offset' => $offset,
                'totalCount' => $count,
            ];

            $data = new Paginator($query->getQuery());
        } else {
            $result['meta'] = [
                'paginated' => false,
                'pageSize' => $count,
                'offset' => $offset,
                'totalCount' => $count,
            ];

            $data = $query->getQuery()->getResult();
        }

        foreach ($data as $row) {
            $result['data'][] = $row->format();
        }

        return $result;
    }
}
