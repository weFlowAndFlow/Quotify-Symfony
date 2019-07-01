<?php

namespace App\Repository;


use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    // /**
    //  * @return Quote[] Returns an array of Quote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quote
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /*
     * Retrieves a random quote from the database or null if there is no quote
     *
     * @throws NonUniqueResultException
     * @return Quote
     */
    public function findRandom()
    {
        $count = $this->createQueryBuilder('q')
            ->select('COUNT(q)')
            ->getQuery()
            ->getSingleScalarResult();

        if($count > 0)
        {
            return $this->createQueryBuilder('q')
                ->setFirstResult(rand(0, $count - 1))
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        }
        else
        {
          return null;
        }
    }


    /*
     * Find all Quotes query
     *
     * @return Query
     */
    public function createQueryFindAll()
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.id', 'DESC')
            ->getQuery();
    }

    public function createQueryFindByOriginalWork($work)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.originalWork = :work')
            ->setParameter('work', $work)
            ->getQuery();
    }

    public function createQueryFindAllByAuthor($author)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ;
    }

    public function createQueryFindAllByCategory($category)
    {
        return $this->createQueryBuilder('q')
            ->join('q.categories', 'c')
            ->andWhere('c = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ;
    }

    /*
     * Find all Quotes by year query
     *
     * @return Query
     */
    public function createQueryFindAllByYear($year)
    {
        if ($year == 9999)
        {
            return $this->createQueryBuilder('q')
                ->join('q.originalWork', 'og')
                ->andWhere('og.year is NULL')
                ->getQuery();
        }
        else
        {
            return $this->createQueryBuilder('q')
                ->join('q.originalWork', 'og')
                ->andWhere('og.year = :val')
                ->setParameter('val', $year)
                ->getQuery();
        }
    }


    public function countQuotesForUndefinedAuthor()
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q)')
            ->andWhere('q.author is NULL')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }


    public function countQuotesForUndefinedWork()
    {
        return $this->createQueryBuilder('q')
            ->select('count(q)')
            ->andWhere('q.originalWork is null')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function countQuotesForUndefinedCategory()
    {
        return $this->createQueryBuilder('q')
            ->select('count(q)')
            ->leftJoin('q.categories', 'c')
            ->andWhere('c is null')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }


    public function createQueryGetQuotesForUndefinedAuthor()
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.author is NULL')
            ->getQuery()
            ;
    }


    public function createQueryGetQuotesForUndefinedWork()
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.originalWork is null')
            ->getQuery()
            ;
    }

    public function createQueryGetQuotesForUndefinedCategory()
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.categories', 'c')
            ->andWhere('c is null')
            ->getQuery()
            ;
    }


}
