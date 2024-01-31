<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UlidType;

/**
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

    public function createArticle(string $title): Article
    {
        $article = new Article($title);
        $this->getEntityManager()->persist($article);

        return $article;
    }

    public function findPaginated(
        array $tags,
        int $page,
        int $perPage
    ): array {
        if ($tags) {
            $queryBuilder = $this->queryByTags($tags);
        } else {
            $queryBuilder = $this->createQueryBuilder('a');
        }

        return $queryBuilder
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getResult();
    }

    /**
     * @param Tag[] $tags
     *
     * @return Article[]
     */
    public function findByTags(array $tags): array
    {
        return $this->queryByTags($tags)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Tag[] $tags
     */
    private function queryByTags(array $tags): QueryBuilder
    {
        $tagIds = array_map(fn(Tag $t) => $t->getId(), $tags);

        return $this->createQueryBuilder('a')
            ->leftJoin('a.articleTags', 'at')
            ->leftJoin('at.tag', 't')
            ->where('t.id IN(:tagIds)')->setParameter('tagIds', $tagIds, UlidType::NAME)
            ->groupBy('a')
            ->having('COUNT(a)=:tagsCount')->setParameter('tagsCount', count($tags));
    }
}
