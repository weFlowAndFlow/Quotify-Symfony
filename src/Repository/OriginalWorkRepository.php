<?php

namespace App\Repository;

use App\Entity\OriginalWork;
use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OriginalWork|null find($id, $lockMode = null, $lockVersion = null)
 * @method OriginalWork|null findOneBy(array $criteria, array $orderBy = null)
 * @method OriginalWork[]    findAll()
 * @method OriginalWork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OriginalWorkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OriginalWork::class);
    }

    // /**
    //  * @return OriginalWork[] Returns an array of OriginalWork objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OriginalWork
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByAuthor($id)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.author.id = :val')
            ->setParameter('val', $id)
            ->orderBy('o.title', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findDates()
    {
        return $this->createQueryBuilder('og')
            ->select('og.year')
            ->distinct(true)
            ->orderBy('og.year', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function createQueryFindAll()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.title')
            ->getQuery();
    }


}
