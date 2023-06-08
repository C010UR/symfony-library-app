<?php

namespace App\Entity;

use App\DoctrineExtensions\DBAL\Types\Citext;
use App\Entity\Interface\EntityInterface;
use App\Repository\UserRepository;
use App\Utils\Utils;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', 'Email is already taken.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Citext::CITEXT, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(options: ['default' => 'not-set'])]
    private ?string $password = 'not-set';

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isActive = false;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $loginAttempts = 0;

    #[ORM\Column(type: Citext::CITEXT)]
    private ?string $firstName = null;

    #[ORM\Column(type: Citext::CITEXT)]
    private ?string $lastName = null;

    #[ORM\Column(type: Citext::CITEXT, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isDeleted = false;

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
            $this->setFirstName(Utils::ucwords($this->getFirstName()));
        }

        if ($this->getLastName()) {
            $this->setLastName(Utils::ucwords($this->getLastName()));
        }

        if ($this->getMiddleName()) {
            $this->setMiddleName(Utils::ucwords($this->getMiddleName()));
        }
    }

    public function computeSlug(SluggerInterface $slugger): void
    {
        $this->slug = (string) $slugger->slug((string) $this)->lower();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLoginAttempts(): ?int
    {
        return $this->loginAttempts;
    }

    public function setLoginAttempts(int $loginAttempts): self
    {
        $this->loginAttempts = $loginAttempts;

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

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getObfuscatedUserIdentifier(): ?string
    {
        if ('' === $this->getUserIdentifier() || '0' === $this->getUserIdentifier()) {
            return null;
        }

        $address = explode('@', $this->getEmail());
        $name = implode('@', array_slice($address, 0, count($address) - 1));
        $length = floor(strlen($name) / 2);

        return substr($name, 0, $length) . str_repeat('*', $length) . '@' . end($address);
    }

    public function format(): array
    {
        return [
            'id' => $this->getId(),
            'fullName' => $this->getFullName(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'middleName' => $this->getMiddleName(),
            'slug' => $this->getSlug(),
            'email' => self::getObfuscatedUserIdentifier(),
            'image' => $this->getImagePath(),
            'roles' => $this->getRoles(),
            'isDeleted' => $this->isDeleted(),
            'isActive' => $this->isActive(),
        ];
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
}
