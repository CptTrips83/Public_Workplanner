<?php

namespace App\Entity;

use App\Repository\PauseKategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PauseKategorieRepository::class)]
class PauseKategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $bezahlt = null;

    #[ORM\OneToMany(mappedBy: 'kategorie', targetEntity: Pause::class)]
    private Collection $pauses;

    #[ORM\Column]
    private ?bool $aktiv = true;

    public function __construct()
    {
        $this->pauses = new ArrayCollection();
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

    public function isBezahlt(): ?bool
    {
        return $this->bezahlt;
    }

    public function setBezahlt(bool $bezahlt): static
    {
        $this->bezahlt = $bezahlt;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return Collection<int, Pause>
     */
    public function getPauses(): Collection
    {
        return $this->pauses;
    }

    public function addPause(Pause $pause): static
    {
        if (!$this->pauses->contains($pause)) {
            $this->pauses->add($pause);
            $pause->setKategorie($this);
        }

        return $this;
    }

    public function removePause(Pause $pause): static
    {
        if ($this->pauses->removeElement($pause)) {
            // set the owning side to null (unless already changed)
            if ($pause->getKategorie() === $this) {
                $pause->setKategorie(null);
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
