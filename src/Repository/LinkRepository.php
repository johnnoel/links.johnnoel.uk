<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function create(Link $link): void
    {
        $this->_em->persist($link);
        $this->_em->flush();
    }

    public function update(Link $link): void
    {
        $this->_em->persist($link);
        $this->_em->flush();
    }

    /**
     * @return array<Link>
     */
    public function fetchLinks(bool $publicOnly = true): array
    {
        $qb = $this->createQueryBuilder('l');
        $qb->addSelect([ 'lm' ])
            ->leftJoin('l.metadata', 'lm')
            ->orderBy('l.created', 'DESC')
        ;

        if ($publicOnly) {
            $qb->where($qb->expr()->eq('l.isPublic', ':is_public'))
                ->setParameter('is_public', true)
            ;
        }

        /** @var array<Link> */
        return $qb->getQuery()->getResult();
    }
}
