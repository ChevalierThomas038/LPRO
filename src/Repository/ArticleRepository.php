<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function pagination($currentPage, $nbPerPage): Paginator
    {
        $query = $this->createQueryBuilder('article')
            ->where('article.published=1')
            ->addOrderBy('article.createAt', 'DESC')
            ->leftJoin('article.comments', 'comment')
            ->addSelect('comment')
            ->leftJoin('article.category', 'category')
            ->addSelect('category')
            ->getQuery()
            ->setFirstResult(($currentPage - 1) * $nbPerPage) // Premier élément de la page
            ->setMaxResults($nbPerPage); // Nombre d'éléments par page

        // Equivalent de getResult() mais un count() sur cet objet retourne le nombre de résultats hors pagination
        return new Paginator($query);
    }

    public function findOneByCategory(int $id)
    {
        return $this->createQueryBuilder('article')
            ->where('category=:category')->setParameter('category', $id)
            ->leftJoin('article.category', 'category')
            ->addSelect('category')
            ->leftJoin('article.comments', 'comment')
            ->addSelect('comment')
            ->getQuery()->getResult();
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
}
