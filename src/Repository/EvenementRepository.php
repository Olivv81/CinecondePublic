<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    // /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }querybuilder entre deux date

    */
    public function eventByDay($jour)
    {
        $date = new \DateTime($jour);

        $query = $this->createQueryBuilder('e')
            ->select('e')
            ->Where(':day BETWEEN e.date AND e.dateFin OR e.date BETWEEN :day AND :to')
            ->setParameter('day', $date->format("Y-m-d") . " 00:00:00")
            ->setParameter('to', $date->format("Y-m-d") . " 23:59:00")
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
        return $query;
    }
    public function eventAfterToday()
    {
        $date = new \DateTime("now");

        $query = $this->createQueryBuilder('e')
            ->select('e')
            ->Where(':day BETWEEN e.date AND e.dateFin OR e.date >=:day')
            ->setParameter('day', $date->format("Y-m-d") . " 00:00:00")
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
        return $query;
    }
}
