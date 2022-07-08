<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function fetchCategories(): array
    {
        return $this->findAll();
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
