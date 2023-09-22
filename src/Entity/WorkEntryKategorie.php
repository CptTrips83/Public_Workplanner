<?php

namespace App\Entity;

use App\Repository\WorkEntryKategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkEntryKategorieRepository::class)]
class WorkEntryKategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\Column(type: Types::BOOLEAN)]
    private $bezahlt = null;

    #[ORM\OneToMany(mappedBy: 'kategorie', targetEntity: WorkEntry::class)]
    private Collection $workentry;

    #[ORM\Column]
    private ?bool $aktiv = true;

    public function __construct()
    {
        $this->workentry = new ArrayCollection();
    }

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

    public function getBezahlt()
    {
        return $this->bezahlt;
    }

    public function setBezahlt($bezahlt): static
    {
        $this->bezahlt = $bezahlt;

        return $this;
    }

    public function isBezahlt(): ?bool
    {
        return $this->bezahlt;
    }

    /**
     * @return Collection<int, WorkEntry>
     */
    public function getWorkentry(): Collection
    {
        return $this->workentry;
    }

    public function addWorkentry(WorkEntry $workentry): static
    {
        if (!$this->workentry->contains($workentry)) {
            $this->workentry->add($workentry);
            $workentry->setKategorie($this);
        }

        return $this;
    }

    public function removeWorkentry(WorkEntry $workentry): static
    {
        if ($this->workentry->removeElement($workentry)) {
            // set the owning side to null (unless already changed)
            if ($workentry->getKategorie() === $this) {
                $workentry->setKategorie(null);
            }
        }

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
}
