<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @param array<string> $tags
     * @return array<Tag>
     */
    public function fetchAndCreateTags(array $tags): array
    {
        $tags = array_unique(array_filter($tags));

        $qb = $this->createQueryBuilder('t');
        $qb->where($qb->expr()->in('t.tag', ':tags'))
            ->setParameters([
                'tags' => $tags,
            ])
        ;

        /** @var array<Tag> $existingTags */
        $existingTags = $qb->getQuery()->getResult();
        $toCreate = array_diff($tags, array_map(fn (Tag $t): string => $t->getTag(), $existingTags));
        $newTags = [];

        if (count($toCreate) > 0) {
            foreach ($toCreate as $t) {
                $newTag = new Tag($t);
                $this->_em->persist($newTag);

                $newTags[] = $newTag;
            }

            $this->_em->flush();
        }

        return array_merge($existingTags, $newTags);
    }
}
