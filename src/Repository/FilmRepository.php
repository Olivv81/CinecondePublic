<?php

namespace App\Repository;

use App\Entity\Film;
use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    // /**
    //  * @return Film[] Returns an array of Film objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Film
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    
    */
    public function getAfterToday($today)
    {
        $entityManager = $this->getEntityManager();
        $date = new \DateTime();

        $query = $this->createQueryBuilder('f')
            ->select('f')
            ->innerJoin('App\Entity\Seance', 's', 'WITH', 'f.id = s.film')
            ->innerJoin('App\Entity\Horaire', 'h', 'WITH', 's.id = h.seance')
            ->andWhere('h.horaire >= :today')
            ->setParameter('today', $date->Format('Y-m-d H:i:s'))
            ->orderBy('h.horaire', 'ASC')

            ->getQuery()
            ->getResult();

        return $query;
    }
    public function moviePerEvent($event)
    {
        $query = $this->createQueryBuilder('f')
            ->select('f')
            ->innerJoin('f.evenements', 'e')
            ->where('e.id= :event')
            ->setParameter('event', $event)
            ->join('f.horaires', 'h')
            ->orderBy('h.horaire', 'ASC')
            ->getQuery()
            ->getResult();
        return $query;
    }
}
