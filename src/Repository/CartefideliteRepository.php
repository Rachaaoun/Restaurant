<?php

namespace App\Repository;

use App\Entity\Cartefidelite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cartefidelite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cartefidelite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cartefidelite[]    findAll()
 * @method Cartefidelite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartefideliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cartefidelite::class);
    }

    // /**
    //  * @return Cartefidelite[] Returns an array of Cartefidelite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cartefidelite
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
