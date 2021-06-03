<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\ModelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"models:read"},"swagger_definition_name"=""},
 *     denormalizationContext={"groups"={"models:write"}, "swagger_definition_name"=""},
 *     attributes={"security"="is_granted('ROLE_USER')"},
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_USER')"},
 *         "post"={
 *                  "security"="is_granted('ROLE_USER')",
 *         "openapi_context"={
 *                  "requestBody"= {
 *                      "content" = {
 *                              "application/json" = {
 *                                      "schema" = {
 *                                          "$ref" = "#components/schemas/Models-models.write"
 *                                       }
 *                               }
 *                       }
 *                   }
 *              }
 *          }
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('get', object)"},
 *         "put"={"security"="is_granted('edit', object)"},
 *         "delete"={"security"="is_granted('delete', object)"},
 *     }
 * )
 * @ORM\Entity(repositoryClass=ModelsRepository::class)
 */
class Models
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("models:read", "user:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("models:read", "models:write", "user:read")
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("models:read", "models:write", "user:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("models:read", "models:write", "user:read")
     */
    private $file;

    /**
     * @return mixed
     */
    public function getFileUrl()
    {
        return $this->file_url;
    }

    /**
     * @param mixed $file_url
     */
    public function setFileUrl($file_url): void
    {
        $this->file_url = $file_url;
    }

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("models:read", "models:write", "user:read")
     */
    private $preview;

    /**
     * @ORM\Column(type="string", length=255)
     * @ApiProperty(writable=false)
     * @Groups("models:read", "user:read")
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     * @ApiProperty(writable=false)
     * @Groups("models:read", "user:read")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="models")
     * @ORM\JoinColumn(nullable=false)
     * @ApiProperty(writable=false)
     * @Groups("models:read")
     */
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function setPreview(string $preview): self
    {
        $this->preview = $preview;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }
}
