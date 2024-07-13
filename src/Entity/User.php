<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $Name = null;

    #[ORM\Column(length: 60)]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    private ?string $Password = null;

    #[ORM\Column(type: 'json')]
    private $Role = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getRoles(): array
    {
        $Role = $this->Role;
        // guarantee every user at least has User
        $Role[] = 'User';

        return array_unique($Role);
    }

    public function setRoles(array $Role): self
    {
        $this->Role = $Role;

        return $this;
    }

    public function setRole(string $Role): static
    {
        $this->Role = $Role;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->getEmail();
    }

    public function eraseCredentials() : void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
}
