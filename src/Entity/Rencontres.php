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
     * @ORM\Column(type="string", length=100)
     */
    private $adversaire;

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
     * @ORM\OneToOne(targetEntity=EquipeRencontre::class, inversedBy="rencontre", cascade={"persist", "remove"})
     */
    private $equipeRencontre;

    /**
     * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="rencontre")
     */
    private $matchs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phase;

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

    public function getAdversaire(): ?string
    {
        return $this->adversaire;
    }

    public function setAdversaire(string $adversaire): self
    {
        $this->adversaire = $adversaire;

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

    public function getEquipeRencontre(): ?EquipeRencontre
    {
        return $this->equipeRencontre;
    }

    public function setEquipeRencontre(?EquipeRencontre $equipeRencontre): self
    {
        $this->equipeRencontre = $equipeRencontre;

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
}
