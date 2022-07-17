<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function create(Category $category): void
    {
        $this->_em->persist($category);
        $this->_em->flush();
    }

    /**
     * @return array<Category>
     */
    public function fetchCategories(bool $publicOnly = true): array
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Category::class, 'c');
        $rsm->addScalarResult('link_count', 'link_count');
        $selectClause = $rsm->generateSelectClause('c', 'c');

        $params = [];
        $sql = 'SELECT ' . $selectClause . ', COUNT(c2l.link_id)
            FROM categories c
            JOIN categories2links c2l ON c2l.category_id = c.id
        ';

        if ($publicOnly) {
            $sql .= '
                JOIN links l ON l.id = c2l.link_id
                WHERE l.is_public = :is_public
            ';
            $params['is_public'] = true;
        }

        $sql .= '
            GROUP BY c.id
            HAVING COUNT(c2l.link_id) > 0
            ORDER BY c.name
        ';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters($params);

        return array_map(fn (array $row): Category => $row[0], $query->getResult());
    }

    /**
     * @param array<string> $slugs
     * @return array<Category>
     */
    public function fetchCategoriesBySlug(array $slugs): array
    {
        $slugs = array_unique(array_filter($slugs));

        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->in('c.slug', ':slugs'))
            ->setParameters([
                'slugs' => $slugs,
            ])
        ;

        /** @var array<Category> */
        return $qb->getQuery()->getResult();
    }
}
