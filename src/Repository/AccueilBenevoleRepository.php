<?php

namespace App\Repository;

use App\Entity\AccueilBenevole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccueilBenevole>
 *
 * @method AccueilBenevole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccueilBenevole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccueilBenevole[]    findAll()
 * @method AccueilBenevole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccueilBenevoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccueilBenevole::class);
    }

    public function add(AccueilBenevole $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccueilBenevole $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AccueilBenevole[] Returns an array of AccueilBenevole objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccueilBenevole
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
