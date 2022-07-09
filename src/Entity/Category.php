<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[Serializer\Exclude]
    private string $id;
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $slug;
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;
    /**
     * @var Collection<int,Link>
     */
    #[ORM\ManyToMany(targetEntity: Link::class, mappedBy: 'categories')]
    #[Serializer\Exclude]
    private Collection $links;

    public function __construct(string $name, string $slug)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->slug = $slug;
        $this->links = new ArrayCollection();
    }

    public static function createSlug(string $name): string
    {
        return strval(preg_replace('/\s+/u', '-', mb_strtolower($name)));
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
