<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('get', object)"},
 *         "put"={"security"="is_granted('edit', object)"},
 *         "delete"={"security"="is_granted('delete', object)"},
 *     }
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     errorPath="username",
 *     message="This username is already used."
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="This email is already used."
 * )
 * @UniqueEntity(
 *     fields={"apiKey"},
 *     errorPath="apiKey",
 *     message="This apiKey is already used."
 * )
 *
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(writable=false)
     * @Groups("user:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @ApiProperty(writable=false)
     * @Groups("user:read")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ApiProperty(readable=false)
     * @Groups("user:write")
     * @SerializedName("password")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 8,
     *     max = 200,
     *     minMessage = "Your password must be at least {{ limit }} characters long.",
     *     maxMessage = "Your password cannot be longer than {{ limit }} characters."
     * )
     * @Assert\NotEqualTo(
     *     propertyPath = "username",
     *     message = "Your password should not be the same as your username."
     * )
     */
    private $plainPassword;


    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Models::class, mappedBy="user", orphanRemoval=true)
     * @ApiProperty(writable=false)
     * @Groups("user:read")
     */
    private $models;

    /**
     * @ORM\Column(type="string",unique=true, length=255, nullable=true)
     */
    private $apiKey;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups("user:write")
     */
    private $accessDomain = [];

    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

    /**
     * @return Collection|self[]
     */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(Models $model): self
    {
        if (!$this->models->contains($model)) {
            $this->models[] = $model;
            $model->setUser($this);
        }

        return $this;
    }

    public function removeModel(Models $model): self
    {
        if ($this->models->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getUser() === $this) {
                $model->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
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
    public function getUsername(): string
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
     * @see UserInterface
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @param string $roles
     * @return bool
     */
    public function hasRoles(string $roles): bool
    {
        return in_array($roles, $this->roles);
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getAccessDomain(): ?array
    {
        return $this->accessDomain;
    }

    public function setAccessDomain(?array $accessDomain): self
    {
        $this->accessDomain = $accessDomain;

        return $this;
    }
}
