<?php

namespace App\Repository;

use App\Entity\DowntimeKpi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DowntimeKpi>
 *
 * @method DowntimeKpi|null find($id, $lockMode = null, $lockVersion = null)
 * @method DowntimeKpi|null findOneBy(array $criteria, array $orderBy = null)
 * @method DowntimeKpi[]    findAll()
 * @method DowntimeKpi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DowntimeKpiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DowntimeKpi::class);
    }

//    /**
//     * @return DowntimeKpi[] Returns an array of DowntimeKpi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DowntimeKpi
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
