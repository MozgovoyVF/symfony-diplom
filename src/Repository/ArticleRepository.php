<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
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

     //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    public function findLatestMonthPublished(int $authorId)
    {
        return $this->published($this->latest())
            ->andWhere('a.author = :author')
            ->setParameter('author', $authorId)
            ->andWhere('a.createdAt >= :month_ago')
            ->setParameter('month_ago', new \DateTime('-1 month'))
            ->getQuery()
            ->getResult();
    }

     //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    public function findLatestHourPublished(int $authorId)
    {
        return $this->published($this->latest())
            ->andWhere('a.author = :author')
            ->setParameter('author', $authorId)
            ->andWhere('a.createdAt >= :hour_ago')
            ->setParameter('hour_ago', new \DateTime('-1 hour'))
            ->getQuery()
            ->getResult();
    }

    public function published()
    {
        return $this->createQueryBuilder('a')->andWhere('a.createdAt IS NOT NULL');
    }
    public function latest()
    {
        return $this
            ->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC');
    }
}
