<?php

namespace App\Repository;

use App\Entity\OriginalWork;
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

    public function getWorkById($id, $user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByAuthor($id)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.author.id = :val')
            ->setParameter('val', $id)
            ->orderBy('o.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDates($user)
    {
        return $this->createQueryBuilder('og')
            ->select('og.year')
            ->distinct(true)
            ->join('og.quotes', 'q')
            ->andWhere('q.originalWork is not null')
            ->andWhere('og.user = :user')
            ->setParameter('user', $user)
            ->orderBy('og.year', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function createQueryFindAll($user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.title')
            ->getQuery();
    }

    public function search($keywords, $user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.title like :val')
            ->setParameter('val', '%'.$keywords.'%')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
            ;

    }


}
