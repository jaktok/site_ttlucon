<?php

namespace App\Entity;

use App\Repository\RencontresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RencontresRepository::class)
 */
class Rencontres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_rencontre;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $domicile;

    /**
     * @ORM\Column(type="integer")
     */
    private $no_journee;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $victoire;

    /**
     * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="rencontre")
     */
    private $matchs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phase;

    /**
     * @ORM\ManyToOne(targetEntity=EquipeType::class, inversedBy="rencontre")
     */
    private $equipeType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $equipeA;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $equipeB;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreA;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreB;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $is_retour;

    /**
     * @ORM\OneToOne(targetEntity=Fichiers::class, inversedBy="rencontres", cascade={"persist", "remove"}) 
     */
    private $fichier;

    public function __construct()
    {
        $this->matchs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRencontre(): ?\DateTimeInterface
    {
        return $this->date_rencontre;
    }

    public function setDateRencontre(\DateTimeInterface $date_rencontre): self
    {
        $this->date_rencontre = $date_rencontre;

        return $this;
    }

    public function getDomicile(): ?bool
    {
        return $this->domicile;
    }

    public function setDomicile(bool $domicile): self
    {
        $this->domicile = $domicile;

        return $this;
    }

    public function getNoJournee(): ?int
    {
        return $this->no_journee;
    }

    public function setNoJournee(int $no_journee): self
    {
        $this->no_journee = $no_journee;

        return $this;
    }

    public function getVictoire(): ?bool
    {
        return $this->victoire;
    }

    public function setVictoire(?bool $victoire): self
    {
        $this->victoire = $victoire;

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getMatchs(): Collection
    {
        return $this->matchs;
    }

    public function addMatch(Matchs $match): self
    {
        if (!$this->matchs->contains($match)) {
            $this->matchs[] = $match;
            $match->setRencontre($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): self
    {
        if ($this->matchs->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getRencontre() === $this) {
                $match->setRencontre(null);
            }
        }

        return $this;
    }

    public function getPhase(): ?int
    {
        return $this->phase;
    }

    public function setPhase(?int $phase): self
    {
        $this->phase = $phase;

        return $this;
    }

    public function getEquipeType(): ?EquipeType
    {
        return $this->equipeType;
    }

    public function setEquipeType(?EquipeType $equipeType): self
    {
        $this->equipeType = $equipeType;

        return $this;
    }

    public function getEquipeA(): ?string
    {
        return $this->equipeA;
    }

    public function setEquipeA(?string $equipeA): self
    {
        $this->equipeA = $equipeA;

        return $this;
    }

    public function getEquipeB(): ?string
    {
        return $this->equipeB;
    }

    public function setEquipeB(?string $equipeB): self
    {
        $this->equipeB = $equipeB;

        return $this;
    }

    public function getScoreA(): ?int
    {
        return $this->scoreA;
    }

    public function setScoreA(?int $scoreA): self
    {
        $this->scoreA = $scoreA;

        return $this;
    }

    public function getScoreB(): ?int
    {
        return $this->scoreB;
    }

    public function setScoreB(?int $scoreB): self
    {
        $this->scoreB = $scoreB;

        return $this;
    }

    public function getIsRetour(): ?string
    {
        return $this->is_retour;
    }

    public function setIsRetour(?string $is_retour): self
    {
        $this->is_retour = $is_retour;

        return $this;
    }

    public function getFichier(): ?Fichiers
    {
        return $this->fichier;
    }

    public function setFichier(?Fichiers $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }
}
