<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
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
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isDeleted = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function computeSlug(SluggerInterface $slugger): void
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
    }

    public function normalizeName(): void
    {
        if ($this->getFirstName()) {
            $this->setFirstName(ucfirst(strtolower($this->getFirstName())));
        }

        if ($this->getLastName()) {
            $this->setLastName(ucfirst(strtolower($this->getLastName())));
        }

        if ($this->getMiddleName()) {
            $this->setMiddleName(ucfirst(strtolower($this->getMiddleName())));
        }
    }

    public function __toString(): ?string
    {
        return $this->getFullName();
    }

    public function getFullName(): ?string
    {
        if (!$this->firstName || !$this->lastName) {
            return null;
        }

        $fullName = $this->lastName.' '.$this->firstName;

        if ($this->middleName) {
            $fullName .= ' '.$this->middleName;
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

    public function format(bool $isWithBooks = false): array
    {
        $result = [
            'id' => $this->getId(),
            'full_name' => $this->getFullName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'middle_name' => $this->getMiddleName(),
            'website' => $this->getWebsite(),
            'email' => $this->getEmail(),
            'slug' => $this->getSlug(),
            'isDeleted' => $this->isDeleted(),
        ];

        if ($isWithBooks) {
            $books = [];

            foreach ($this->getBooks() as $book) {
                $books[] = $book->format();
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
        return $this->books;
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
}
