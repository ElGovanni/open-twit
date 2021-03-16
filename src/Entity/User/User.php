<?php

namespace App\Entity\User;

use App\Repository\User\UserRepository;
use App\ValueObject\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="client")
 */
#[UniqueEntity('username', groups: ['create'])]
#[UniqueEntity('email', groups: ['create'])]
class User implements UserInterface
{
    public const ALPHA_NUM_ONESPACE = '/^(?!\d)[a-zA-Z\d]+(?: [a-zA-Z\d]+)*$/';

    public const SECURE_PASSWORD_PATTERN = '/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/';

    public const READ = 'user:read';

    public const CREATE = 'user:create';

    public const UPDATE = 'user:update';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    #[Groups(['user:read'])]
    private Uuid $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    #[NotBlank(groups: [self::CREATE])]
    #[Length(min: 3, max: 20)]
    #[Groups([self::CREATE, self::UPDATE])]
    private string $username;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    #[Assert\Regex(self::ALPHA_NUM_ONESPACE)]
    #[Groups([self::CREATE, self::READ, self::UPDATE])]
    private ?string $displayName = null;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    #[NotBlank(groups: [self::CREATE])]
    #[Email(groups: [self::CREATE])]
    #[Length(max: 60)]
    #[Groups([self::CREATE, self::UPDATE, self::READ])]
    private string $email;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private ?array $roles = [Role::INACTIVE];

    #[Groups([self::CREATE])]
    #[SerializedName('roles')]
    private array $rolesHierarchy = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    #[NotBlank(groups: [self::CREATE])]
    #[Assert\Regex(
        pattern: self::SECURE_PASSWORD_PATTERN,
        message: 'Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter',
        groups: [self::CREATE]),
    ]
    #[SerializedName('password')]
    #[Groups([self::READ])]
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $enabled = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[Groups([self::UPDATE, self::READ])]
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $confirmationToken = null;

    public function __construct(Uuid $id = null)
    {
        $this->id = $id ?? Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
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

    public function getRolesHierarchy(): array
    {
        return $this->rolesHierarchy;
    }

    public function setRolesHierarchy(array $rolesHierarchy): self
    {
        $this->rolesHierarchy = $rolesHierarchy;
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName ? $this->displayName : $this->username;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }
}
