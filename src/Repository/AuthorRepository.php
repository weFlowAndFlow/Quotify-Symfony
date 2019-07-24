<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    // /**
    //  * @return Author[] Returns an array of Author objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAuthorById($id, $user)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function createQueryFindAll($user)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    public function createQuerySearch($keywords, $user)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.forename like :val')
            ->setParameter('val', '%'.$keywords.'%')
            ->orWhere('a.name like :val2')
            ->setParameter('val2', '%'.$keywords.'%')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ;

    }

    public function searchResultsSize($keywords, $user)
    {
        return $this->createQueryBuilder('a')
            ->select('count(a)')
            ->andWhere('a.forename like :val')
            ->setParameter('val', '%'.$keywords.'%')
            ->orWhere('a.name like :val2')
            ->setParameter('val2', '%'.$keywords.'%')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
            ;

    }

}
