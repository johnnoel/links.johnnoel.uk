<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LinkRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[ORM\Table(name: 'links')]
class Link
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;
    #[ORM\Column(type: 'text', nullable: false)]
    private string $url;
    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $created;
    /**
     * @var Collection<int,Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'links')]
    #[ORM\JoinTable(name: 'categories2links')]
    #[Serializer\Exclude]
    private Collection $categories;
    /**
     * @var Collection<int,Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'links')]
    #[ORM\JoinTable(name: 'tags2links')]
    #[Serializer\Exclude]
    private Collection $tags;

    /**
     * @param array<Tag> $tags
     */
    public function __construct(string $url, array $tags = [])
    {
        $this->id = Uuid::uuid4()->toString();
        $this->url = $url;
        $this->created = new DateTimeImmutable('now');
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection($tags);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return Collection<int,Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return array<string>
     */
    #[Serializer\VirtualProperty()]
    #[Serializer\SerializedName('tags')]
    public function getTagStrings(): array
    {
        return $this->tags->map(fn (Tag $t): string => $t->getTag())->toArray();
    }
}
