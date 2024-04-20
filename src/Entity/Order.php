<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Repository\OrderRepository;
use App\Utils\Utils;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order implements EntityInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'citext')]
    private $firstName;

    #[ORM\Column(type: 'citext')]
    private $lastName;

    #[ORM\Column(type: 'citext', nullable: true)]
    private $middleName;

    #[ORM\Column(type: 'citext')]
    private $phoneNumber;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne]
    private ?User $userCompleted = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCompleted = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isDeleted = false;

    #[ORM\Column]
    private ?int $quantity = null;

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

    public function normalizeName(): void
    {
        if ($this->getFirstName()) {
            $this->setFirstName(Utils::ucwords(Utils::uclower($this->getFirstName())));
        }

        if ($this->getLastName()) {
            $this->setLastName(Utils::ucwords(Utils::uclower($this->getLastName())));
        }

        if ($this->getMiddleName()) {
            $this->setMiddleName(Utils::ucwords(Utils::uclower($this->getMiddleName())));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(): static
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }

    public function getUserCompleted(): ?User
    {
        return $this->userCompleted;
    }

    public function setUserCompleted(?User $userCompleted): static
    {
        $this->userCompleted = $userCompleted;

        $this->dateCompleted = $userCompleted instanceof \App\Entity\User ? new \DateTime() : null;

        return $this;
    }

    public function getDateCompleted(): ?\DateTimeInterface
    {
        return $this->dateCompleted;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function format(bool $isDeleted = false): array
    {
        return [
            'id' => $this->getId(),
            'book' => $this->getBook()->format($isDeleted),
            'fullName' => $this->getFullName(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'middleName' => $this->getMiddleName(),
            'phoneNumber' => $this->getPhoneNumber(),
            'quantity' => $this->getQuantity(),
            'dateCreated' => $this->getDateCreated()->format(\DateTime::ATOM),
            'userCompleted' => $this->getUserCompleted() instanceof User ? $this->getUserCompleted()->format() : null,
            'dateCompleted' => $this->getDateCompleted() instanceof \DateTimeInterface ? $this->getDateCompleted()->format(\DateTime::ATOM) : null,
            'isDeleted' => $this->isDeleted(),
        ];
    }

    public function isDeleted(): bool
    {
        return (bool) $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
