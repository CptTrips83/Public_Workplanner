<?php

namespace App\Entity;

use App\Repository\UserRolesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRolesRepository::class)]
class UserRoles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $aktiv = true;

    #[ORM\Column(length: 255)]
    private ?string $securityName = null;

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isAktiv(): ?bool
    {
        return $this->aktiv;
    }

    public function setAktiv(bool $aktiv): static
    {
        $this->aktiv = $aktiv;

        return $this;
    }

    public function getSecurityName(): ?string
    {
        return $this->securityName;
    }

    public function setSecurityName(string $securityName): static
    {
        $this->securityName = $securityName;

        return $this;
    }
}
