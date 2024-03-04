<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

//    /**
//     * @return Article[] Returns an array of Article objects
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

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function save(Article $entity, bool $flush = false): void
{
    $this->getEntityManager()->persist($entity);

    if ($flush) {
        $this->getEntityManager()->flush();
    }
}

public function remove(Article $entity, bool $flush = false): void
{
    $this->getEntityManager()->remove($entity);

    if ($flush) {
        $this->getEntityManager()->flush();
    }
}
public function chart_repository(){
    return  $this->createQueryBuilder('r')
             -> select('r.date, COUNT(r.id) as count')
          
               ->groupBy('r.date')  
               ->getQuery()
               ->getResult()
           ;
   }
   public function trie3()
   {
       return $this->createQueryBuilder('article')
       ->setMaxResults(5)
           ->orderBy('article.date', 'ASC')
        
           ->getQuery()
           ->getResult();
   }
   public function findByTitleAndOrder($searchTerm, $order)
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($searchTerm)) {
            $qb->andWhere('a.title LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $qb->orderBy('a.date', $order);

        return $qb->getQuery()->getResult();
    }

}
