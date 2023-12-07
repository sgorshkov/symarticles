<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Symfony\Component\Uid\Ulid;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function createTag(string $name): Tag
    {
        $tag = new Tag($name);
        $this->getEntityManager()->persist($tag);

        return $tag;
    }

    /**
     * @param string[] $ids
     */
    public function findByIds(array $ids): array
    {
        if (!$ids) {
            throw new RuntimeException('Tag ids array can not be empty');
        }

        $ulidIds = array_map(fn(string $ulid) => Ulid::fromRfc4122($ulid)->toBinary(), $ids);

        return $this->createQueryBuilder('t')
            ->where('t.id IN(:ids)')->setParameter('ids', $ulidIds)
            ->getQuery()->getResult();
    }

    public function findPaginated(int $page, int $perPage): array
    {
        return $this->findBy([], null, $perPage, ($page - 1) * $perPage);
    }
}
