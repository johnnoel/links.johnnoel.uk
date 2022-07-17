<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Link;
use App\Entity\LinkMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata(Link::class, 'l');
        $rsm->addJoinedEntityFromClassMetadata(LinkMetadata::class, 'lm', 'l', 'metadata', [ 'id' => 'metadata_id' ]);
        $rsm->addScalarResult('category_aliases', 'category_aliases');
        $selectClause = $rsm->generateSelectClause([
            'l' => 'l',
            'lm' => 'lm',
        ]);

        $params = [];
        $platform = $this->_em->getConnection()->getDatabasePlatform();
        $aggMethod = ($platform instanceof SqlitePlatform) ? 'group_concat' : 'string_agg';
        $sql = 'SELECT ' . $selectClause . ', ' . $aggMethod . '(c.slug, \',\') AS category_aliases
            FROM links l
            LEFT JOIN link_metadata lm ON lm.link_id = l.id
            LEFT JOIN categories2links c2l ON c2l.link_id = l.id
            LEFT JOIN categories c ON c.id = c2l.category_id
        ';

        if ($publicOnly) {
            $sql .= '
                WHERE l.is_public = :is_public
            ';

            $params['is_public'] = true;
        }

        $sql .= '
            GROUP BY l.id, lm.id
            ORDER BY l.created DESC
        ';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters($params);
        /** @var array<array{ 0: Link, category_aliases: string|null }> $result */
        $result = $query->getResult();

        /** @var array<Link> */
        return array_map(fn (array $row): Link => $row[0]->setCategoryAliases($row['category_aliases']), $result);
    }
}
