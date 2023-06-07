<?php

namespace App\Entity;

use App\DoctrineExtensions\DBAL\Types\Citext;
use App\Entity\Interface\EntityInterface;
use App\Repository\AuthorRepository;
use App\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\UniqueConstraint(
    columns: ['first_name', 'last_name', 'middle_name']
)]
#[UniqueEntity(
    fields: ['firstName', 'lastName', 'middleName'],
    message: 'Name is already taken.'
)]
#[UniqueEntity('slug', 'Slug is already taken.')]
class Author implements EntityInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Citext::CITEXT)]
    private ?string $firstName = null;

    #[ORM\Column(type: Citext::CITEXT)]
    private ?string $lastName = null;

    #[ORM\Column(type: Citext::CITEXT, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(type: Citext::CITEXT, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isDeleted = false;

    #[ORM\Column(type: Citext::CITEXT, nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function computeSlug(SluggerInterface $slugger): void
    {
        $this->slug = (string) $slugger->slug((string) $this)->lower();
    }

    public function normalizeName(): void
    {
        if ($this->getFirstName()) {
            $this->setFirstName(Utils::ucwords($this->getFirstName()));
        }

        if ($this->getLastName()) {
            $this->setLastName(Utils::ucwords($this->getLastName()));
        }

        if ($this->getMiddleName()) {
            $this->setMiddleName(Utils::ucwords($this->getMiddleName()));
        }
    }

    public function __toString(): string
    {
        return $this->getFullName() ?? '';
    }

    public function getFullName(): ?string
    {
        if (!$this->getFirstName() || !$this->getLastName()) {
            return null;
        }

        $fullName = $this->getFirstName() . ' ' . $this->getLastName();

        if ($this->getMiddleName()) {
            $fullName .= ' ' . $this->getMiddleName();
        }

        return $fullName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

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

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function format(bool $isWithBooks = true): array
    {
        $result = [
            'id' => $this->getId(),
            'fullName' => $this->getFullName(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'middleName' => $this->getMiddleName(),
            'website' => $this->getWebsite(),
            'email' => $this->getEmail(),
            'image' => $this->getImagePath(),
            'slug' => $this->getSlug(),
            'isDeleted' => $this->isDeleted(),
        ];

        if ($isWithBooks) {
            $books = [];

            foreach ($this->getBooks() as $book) {
                $books[] = $book->format(false);
            }

            $result['books'] = $books;
        }

        return $result;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books->filter(static fn(Book $book) => !$book->isDeleted());
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            $book->removeAuthor($this);
        }

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
}
