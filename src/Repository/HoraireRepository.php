<?php

namespace App\Repository;

use App\Entity\Horaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Horaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Horaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Horaire[]    findAll()
 * @method Horaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horaire::class);
    }

    // /**
    //  * @return Horaire[] Returns an array of Horaire objects
    //  */

    // public function findByUser($user)
    // {
    //     $query = $this->createQueryBuilder('h')
    //         ->select('h')
    //         ->having('h.accueil = :user')
    //         ->setParameter('user', $user)
    //         ->orderBy('h.horaire', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    //     return $query;
    // }


    /*
    public function findOneBySomeField($value): ?Horaire
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function FilmByOneHoraire($today)
    {
        // $date = new \DateTime();

        $query = $this->createQueryBuilder('h')
            ->Select('h')
            ->Where('h.horaire >= :today')
            ->setParameter('today', $today->format("Y-m-d") . " 00:00:00")
            ->orderBy('h.horaire', 'ASC')
            ->Join('h.Film', 'f')

            ->getQuery()
            ->getResult();
        return $query;
    }


    public function FilmDetail($film)
    {
        $query = $this->createQueryBuilder('h')

            ->select('h')
            ->where('f.id=:film')
            ->Join('h.Film', 'f')
            ->setParameter('film', $film)
            ->getQuery()
            ->getResult();
        return $query;
    }


    public function movieByDay($jour)
    {
        $date = new \DateTime($jour);
        // $day = $date->format("Y-m-d") . " 00:00:00";
        // dd($day);

        $query = $this->createQueryBuilder('h')
            ->select('h')
            ->Where('h.horaire > :from')
            ->andWhere('h.horaire <:to')
            ->setParameter('from', $date->format("Y-m-d") . " 00:00:00")
            ->setParameter('to', $date->format("Y-m-d") . " 23:59:00")
            ->Join('h.Film', 'f')
            ->groupBy('f.titre')
            ->orderBy('h.horaire', 'ASC')
            ->getQuery()
            ->getResult();
        return $query;
    }


    public function schedulebymovie($film, $today)
    {
        $query = $this->createQueryBuilder('h')
            ->select('h')
            ->Where('h.horaire >= :today')
            ->andWhere('f.id = :film')
            ->setParameter('today', $today->Format('Y-m-d H:i:s'))
            ->setParameter('film', $film)
            ->Join('h.Film', 'f')
            ->orderBy('h.horaire', 'ASC')
            ->getQuery()
            ->getResult();
        return $query;
    }

    public function HoraireAfterToday()
    {
        $date = new \DateTime("now");
        $query = $this->createQueryBuilder('h')

            ->select('h')
            ->Where('h.horaire >= :today')
            ->setParameter('today', $date->format("Y-m-d") . " 00:00:00")
            ->orderBy('h.horaire', 'ASC')
            ->getQuery()
            ->getResult();
        return $query;
    }
}
