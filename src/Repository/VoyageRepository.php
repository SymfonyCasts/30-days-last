<?php

namespace App\Repository;

use App\Entity\Voyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voyage>
 *
 * @method Voyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voyage[]    findAll()
 * @method Voyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voyage::class);
    }

    /**
     * @return Voyage[]
     */
    public function findBySearch(?string $query, array $searchPlanets, int $limit = null): array
    {
        $qb =  $this->findBySearchQueryBuilder($query, $searchPlanets);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findBySearchQueryBuilder(?string $query, array $searchPlanets, ?string $sort = null, string $direction = 'DESC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('v');

        if ($query) {
            $qb->andWhere('v.purpose LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        if ($searchPlanets) {
            $qb->andWhere('v.planet IN (:planets)')
                ->setParameter('planets', $searchPlanets);
        }

        if ($sort) {
            $qb->orderBy('v.' . $sort, $direction);
        }

        return $qb;
    }
}
