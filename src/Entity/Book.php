<?php

namespace App\Entity;

use App\DoctrineExtensions\DBAL\Types\Citext;
use App\Entity\Interface\EntityInterface;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[UniqueEntity('name', 'Name is already taken.')]
#[UniqueEntity('slug', 'Slug is already taken.')]
class Book implements EntityInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Citext::CITEXT)]
    private ?string $name = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePublished = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publisher $publisher = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'books')]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    private Collection $authors;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isDeleted = false;

    #[ORM\Column(type: Citext::CITEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $pageCount = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function computeSlug(SluggerInterface $slugger): void
    {
        $this->slug = (string) $slugger->slug((string) $this)->lower();
    }

    public function normalizeName(): void
    {
        // if ($this->getName()) {
        //     $this->setName(Utils::ucwords($this->getName()));
        // }
    }

    public function __toString(): string
    {
        if (!$this->getName()) {
            return '';
        }

        $authors = '';

        foreach ($this->getAuthors() as $author) {
            $authors .= $author->getFullName() . ' ';
        }

        return $authors . $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDatePublished(): ?\DateTimeInterface
    {
        return $this->datePublished;
    }

    public function setDatePublished(\DateTimeInterface $datePublished): self
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    public function isDeleted(): bool
    {
        return (bool) $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(bool $isDeleted = false): Collection
    {
        if ($isDeleted) {
            return $this->tags;
        }

        return $this->tags->filter(static fn (Tag $tag) => !$tag->isDeleted());
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(bool $isDeleted = false): Collection
    {
        if ($isDeleted) {
            return $this->authors;
        }

        return $this->authors->filter(static fn (Author $author) => !$author->isDeleted());
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function format(bool $isDeleted = false): array
    {
        $result = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'image' => $this->getImagePath(),
            'slug' => $this->getSlug(),
            'isDeleted' => $this->isDeleted(),
            'publisher' => $this->getPublisher()->format(),
            'datePublished' => $this->getDatePublished()->format(\DateTimeImmutable::ATOM),
            'pageCount' => $this->getPageCount(),
            'description' => $this->getDescription(),
        ];

        $authors = [];

        foreach ($this->getAuthors($isDeleted) as $author) {
            $authors[] = $author->format();
        }

        $result['authors'] = $authors;

        $tags = [];

        foreach ($this->getTags($isDeleted) as $tag) {
            $tags[] = $tag->format();
        }

        $result['tags'] = $tags;

        return $result;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(int $pageCount): self
    {
        $this->pageCount = $pageCount;

        return $this;
    }
}
