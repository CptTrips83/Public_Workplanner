<?php

namespace App\Entity;

use App\Repository\PauseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PauseRepository::class)]
class Pause
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDatum = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endeDatum = null;

    #[ORM\ManyToOne(inversedBy: 'pauses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkEntry $workentry = null;

    #[ORM\ManyToOne(inversedBy: 'pauses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PauseKategorie $kategorie = null;

    #[ORM\Column]
    private ?bool $aktiv = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDatum(): ?\DateTimeInterface
    {
        return $this->startDatum;
    }

    public function setStartDatum(\DateTimeInterface $startDatum): static
    {
        $this->startDatum = $startDatum;

        return $this;
    }

    public function getEndeDatum(): ?\DateTimeInterface
    {
        return $this->endeDatum;
    }

    public function setEndeDatum(?\DateTimeInterface $endeDatum): static
    {
        $this->endeDatum = $endeDatum;

        return $this;
    }

    public function getWorkentry(): ?WorkEntry
    {
        return $this->workentry;
    }

    public function setWorkentry(?WorkEntry $workentry): static
    {
        $this->workentry = $workentry;

        return $this;
    }

    public function getKategorie(): ?PauseKategorie
    {
        return $this->kategorie;
    }

    public function setKategorie(?PauseKategorie $kategorie): static
    {
        $this->kategorie = $kategorie;

        return $this;
    }

    public function getPausenZeit() : int
    {
        return ($this->getEndeDatum() != null ? ($this->getEndeDatum()->getTimestamp() - $this->getStartDatum()->getTimestamp()) : (time() - $this->getStartDatum()->getTimestamp()));
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
