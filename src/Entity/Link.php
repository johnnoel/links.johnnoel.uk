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
    #[ORM\Column(type: 'boolean', options: [ 'default' => false ])]
    private bool $isPublic = false;
    #[ORM\OneToOne(mappedBy: 'link', targetEntity: LinkMetadata::class, cascade: [ 'persist', 'remove' ])]
    private ?LinkMetadata $metadata = null;
    /**
     * @var Collection<int,Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'links', cascade: [ 'persist', 'remove' ])]
    #[ORM\JoinTable(name: 'categories2links')]
    #[Serializer\Type('ArrayCollection<Category>')]
    private Collection $categories;
    /**
     * @var Collection<int,Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'links', cascade: [ 'persist', 'remove' ])]
    #[ORM\JoinTable(name: 'tags2links')]
    #[Serializer\Exclude]
    private Collection $tags;

    /**
     * @param array<Category> $categories
     * @param array<Tag> $tags
     */
    public function __construct(string $url, array $categories = [], array $tags = [], bool $isPublic = false)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->url = $url;
        $this->isPublic = $isPublic;
        $this->created = new DateTimeImmutable('now');
        $this->categories = new ArrayCollection($categories);
        $this->tags = new ArrayCollection($tags);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @return Collection<int,Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function overrideCreated(DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    public function attachMetadata(LinkMetadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function hasMetadata(): bool
    {
        return $this->metadata !== null;
    }

    public function getDomain(): string
    {
        return strval(parse_url($this->url, PHP_URL_HOST));
    }

    #[Serializer\VirtualProperty]
    public function getTitle(): ?string
    {
        return ($this->metadata !== null) ? $this->metadata->getTitle() : null;
    }

    #[Serializer\VirtualProperty]
    public function getDescription(): ?string
    {
        return ($this->metadata !== null) ? $this->metadata->getDescription() : null;
    }

    /**
     * @return array<string>
     */
    #[Serializer\VirtualProperty]
    #[Serializer\SerializedName('tags')]
    public function getTagStrings(): array
    {
        return $this->tags->map(fn (Tag $t): string => $t->getTag())->toArray();
    }
}
