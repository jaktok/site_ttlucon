<?php

namespace App\Entity;

use App\Repository\MatchsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatchsRepository::class)
 */
class Matchs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $victoire;

    /**
     * @ORM\ManyToOne(targetEntity=Joueurs::class, inversedBy="matchs")
     */
    private $joueur;

    /**
     * @ORM\ManyToOne(targetEntity=Competition::class, inversedBy="matchs")
     */
    private $competition;

    /**
     * @ORM\ManyToOne(targetEntity=Rencontres::class, inversedBy="matchs")
     */
    private $rencontre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $matchDouble;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $double1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $double2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_joueur1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_joueur2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $score2;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $victoire2;

    /**
     * @ORM\ManyToMany(targetEntity=Joueurs::class, inversedBy="double1")
     */
    private $joueur1;

    public function __construct()
    {
        $this->joueur_double1 = new ArrayCollection();
        $this->joueur1 = new ArrayCollection();
        $this->joueur2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getJoueur(): ?Joueurs
    {
        return $this->joueur;
    }

    public function setJoueur(?Joueurs $joueur): self
    {
        $this->joueur = $joueur;

        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;

        return $this;
    }

    public function getRencontre(): ?Rencontres
    {
        return $this->rencontre;
    }

    public function setRencontre(?Rencontres $rencontre): self
    {
        $this->rencontre = $rencontre;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getMatchDouble(): ?bool
    {
        return $this->matchDouble;
    }

    public function setMatchDouble(?bool $matchDouble): self
    {
        $this->matchDouble = $matchDouble;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getDouble1(): ?string
    {
        return $this->double1;
    }

    public function setDouble1(?string $double1): self
    {
        $this->double1 = $double1;

        return $this;
    }

    public function getDouble2(): ?string
    {
        return $this->double2;
    }

    public function setDouble2(?string $double2): self
    {
        $this->double2 = $double2;

        return $this;
    }

    public function getIdJoueur1(): ?int
    {
        return $this->id_joueur1;
    }

    public function setIdJoueur1(?int $id_joueur1): self
    {
        $this->id_joueur1 = $id_joueur1;

        return $this;
    }

    public function getIdJoueur2(): ?int
    {
        return $this->id_joueur2;
    }

    public function setIdJoueur2(?int $id_joueur2): self
    {
        $this->id_joueur2 = $id_joueur2;

        return $this;
    }

    public function getScore2(): ?string
    {
        return $this->score2;
    }

    public function setScore2(?string $score2): self
    {
        $this->score2 = $score2;

        return $this;
    }

    public function getVictoire2(): ?bool
    {
        return $this->victoire2;
    }

    public function setVictoire2(?bool $victoire2): self
    {
        $this->victoire2 = $victoire2;

        return $this;
    }

    /**
     * @return Collection|Joueurs[]
     */
    public function getJoueur1(): Collection
    {
        return $this->joueur1;
    }

    public function addJoueur1(Joueurs $joueur1): self
    {
        if (!$this->joueur1->contains($joueur1)) {
            $this->joueur1[] = $joueur1;
        }

        return $this;
    }

    public function removeJoueur1(Joueurs $joueur1): self
    {
        $this->joueur1->removeElement($joueur1);

        return $this;
    }

}
