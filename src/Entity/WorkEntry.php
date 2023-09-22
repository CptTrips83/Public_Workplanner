<?php

namespace App\Entity;

use App\Repository\WorkEntryRepository;
use App\Entity\PauseKategorie;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Twig\Node\Expression\Test\NullTest;

#[ORM\Entity(repositoryClass: WorkEntryRepository::class)]
class WorkEntry
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDatum = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endeDatum = null;
        
    
    #[ORM\ManyToOne(inversedBy: 'workentry')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'workentry')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkEntryKategorie $kategorie = null;

    #[ORM\OneToMany(mappedBy: 'workentry', targetEntity: Pause::class)]
    private Collection $pauses;

    #[ORM\Column]
    private ?bool $aktiv = true;

    /*
    private int $arbeitsZeit = 0;
    private int $pausenZeitBezahlt = 0;
    private int $pausenZeitUnbezahlt = 0;

    public function refresh() : void
    {
        $arbeitsZeit
    }
    */
    public function __construct()
    {
        $this->pauses = new ArrayCollection();
    }

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

    public function getSummeGesamt(): ?int
    {
        return ($this->getEndeDatum() != null ? ($this->getEndeDatum()->getTimestamp() - $this->getStartDatum()->getTimestamp()) : (time() - $this->getStartDatum()->getTimestamp()));
    }    

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getKategorie(): ?WorkEntryKategorie
    {
        return $this->kategorie;
    }

    public function setKategorie(?WorkEntryKategorie $kategorie): static
    {
        $this->kategorie = $kategorie;

        return $this;
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
            $pause->setWorkentry($this);
        }

        return $this;
    }

    public function removePause(Pause $pause): static
    {
        if ($this->pauses->removeElement($pause)) {
            // set the owning side to null (unless already changed)
            if ($pause->getWorkentry() === $this) {
                $pause->setWorkentry(null);
            }
        }

        return $this;
    }

    public function getArbeitsZeit() : int
    {
        $result = $this->getSummeGesamt() - $this->getPausenZeitUnbezahlt();
        
        if($result < 0) $result = 0;

        return $result;
    }

    private function getPausenZeit(Pause $pause) : int
    {
        $result = 0;

        $startDatum = $pause->getStartDatum()->getTimestamp();
        $endeDatum = time();

        if($pause->getEndeDatum() != null) $endeDatum = $pause->getEndeDatum()->getTimestamp();

        $result = $endeDatum - $startDatum;

        if(!$pause->isAktiv()) $result = 0;

        return $result;
    }

    public function getPausenZeitBezahlt() : int
    {
        $result = 0;

        foreach($this->getPauses() as $key => $pause)
            {                
                $pauseKategorie = $pause->getKategorie();
                if($pauseKategorie->isBezahlt())
                {
                    $result = $this->getPausenZeit($pause);
                }

            }

        return $result;
    }

    public function getPausenZeitUnbezahlt() : int
    {
        $result = 0;

        foreach($this->getPauses() as $key => $pause)
            {                
                $pauseKategorie = $pause->getKategorie();
                if(!$pauseKategorie->isBezahlt())
                {
                    $result = $this->getPausenZeit($pause);
                }

            }

        return $result;
    }

    public function getAktivePause() : Pause|null
    {
        $result = null;

        foreach($this->getPauses() as $key => $pause)
        {
            if($pause->getEndeDatum() == null)
            {
                $result = $pause;
                break;
            }
        }

        return $result;
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
