<?php

namespace App\Entity\User;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Users\ProfilePictureAction;
use App\Repository\User\ProfilePictureRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfilePictureRepository::class)
 * @ORM\Table
 */
#[ApiResource(
    collectionOperations: [
        "post" => [
            "controller" => ProfilePictureAction::class,
            "deserialize" => false,
            "security" => "is_granted('ROLE_USER')",
            "validation_groups" => [self::CREATE],
            "openapi_context" => [
                "requestBody" => [
                    "content" => [
                        "multipart/form-data" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "file" => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        "get",
    ],
    iri: "http://schema.org/MediaObject",
    itemOperations: [
        "get",
    ],
    normalizationContext: ["groups" => [self::READ]],
    routePrefix: "/users"
)]
class ProfilePicture
{
    public const READ = 'file:read';

    public const CREATE = 'file:create';

    public const UPDATE = 'file:update';

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     */
    private Uuid $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private string $fileName;

    #[Assert\NotBlank(groups: [self::CREATE])]
    #[Assert\File(maxSize: '1mi', mimeTypes: ['image/*'])]
    #[Groups([self::CREATE])]
    private File $file;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist"})
     */
    private User $user;

    #[SerializedName('absolutePath')]
    #[Groups([self::READ])]
    public string $absolutePath;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        $this->user->setProfilePicture($this);
        return $this;
    }

    public function setAbsolutePath(string $path)
    {
        $this->absolutePath = $path;
    }
}
