<?php

namespace App\Repository;

use App\Entity\ContactNL;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ContactNL>
 *
 * @method ContactNL|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactNL|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactNL[]    findAll()
 * @method ContactNL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactNLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactNL::class);
    }

    public function add(ContactNL $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactNL $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function Destinataires($newsLetter)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT DISTINCT contact_nl.e_mail FROM contact_nl
        LEFT JOIN news_letter_contact_nl ON contact_nl.id=news_letter_contact_nl.contact_nl_id
        INNER JOIN news_letter ON news_letter_contact_nl.news_letter_id=news_letter.id AND  news_letter.id <> 2
        LIMIT 1
                    ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['newsletter' => $newsLetter]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();




        // $query = $this->createQueryBuilder('c')
        //     ->select('c')

        //     ->leftjoin('c.newsLetters', 'l', Join::WITH, $this->expr()->andX('l;id= :newsletter'), ('l.etat<> :envoie')))
        //     // ->Where('l.etat <>:envoie')
        //     ->setParameter('newsletter', $newsLetter)
        //     ->setParameter('envoie', "envoyé")
        //     ->setMaxResults(2)
        //     ->getQuery()
        //     ->getResult();

        // return $query;
    }
    //    /**
    //     * @return ContactNL[] Returns an array of ContactNL objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ContactNL
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
