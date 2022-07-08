<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[ORM\Table(name: 'tags')]
class Tag
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[Serializer\Exclude]
    private string $id;
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $tag;
    /**
     * @var Collection<int,Link>
     */
    #[ORM\ManyToMany(targetEntity: Link::class, mappedBy: 'categories')]
    #[Serializer\Exclude]
    private Collection $links;

    public function __construct(string $tag)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->tag = $tag;
        $this->links = new ArrayCollection();
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
