<?php

namespace App\Entity;

use App\Repository\WorkEntryChangeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryChangeRepository::class)]
class EntryChange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $feldName = null;

    #[ORM\Column(length: 255)]
    private ?string $alterWert = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $neuerWert = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datum = null;

    #[ORM\Column]
    private ?int $entryId = null;

    #[ORM\Column(length: 255)]
    private ?string $Entity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeldName(): ?string
    {
        return $this->feldName;
    }

    public function setFeldName(string $feldName): static
    {
        $this->feldName = $feldName;

        return $this;
    }

    public function getAlterWert(): ?string
    {
        return $this->alterWert;
    }

    public function setAlterWert(string $alterWert): static
    {
        $this->alterWert = $alterWert;

        return $this;
    }

    public function getNeuerWert(): ?string
    {
        return $this->neuerWert;
    }

    public function setNeuerWert(?string $neuerWert): static
    {
        $this->neuerWert = $neuerWert;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDatum(): ?\DateTimeInterface
    {
        return $this->datum;
    }

    public function setDatum(\DateTimeInterface $datum): static
    {
        $this->datum = $datum;

        return $this;
    }

    public function getEntryId(): ?int
    {
        return $this->entryId;
    }

    public function setEntryId(int $workEntryId): static
    {
        $this->entryId = $workEntryId;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->Entity;
    }

    public function setEntity(string $Entity): static
    {
        $this->Entity = $Entity;

        return $this;
    }
}
