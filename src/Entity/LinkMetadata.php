<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'link_metadata')]
class LinkMetadata
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;
    #[ORM\OneToOne(inversedBy: 'metadata', targetEntity: Link::class)]
    #[ORM\JoinColumn(name: 'link_id', referencedColumnName: 'id')]
    private Link $link;
    #[ORM\Column(type: 'text')]
    private string $title;
    #[ORM\Column(type: 'text')]
    private string $description;
    /**
     * @var array<string,mixed>
     */
    #[ORM\Column(type: 'json', options: [ 'jsonb' => true ])]
    private array $extra;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $fetched;

    /**
     * @param array<string,mixed> $extra
     */
    public function __construct(Link $link, string $title, string $description, array $extra = [])
    {
        $this->id = Uuid::uuid4()->toString();
        $this->link = $link;
        $this->title = $title;
        $this->description = $description;
        $this->extra = $extra;
        $this->fetched = new DateTimeImmutable('now');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
