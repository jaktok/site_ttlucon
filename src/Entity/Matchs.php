<?php

namespace App\Entity;

use App\Repository\MatchsRepository;
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
     * @ORM\Column(type="integer", nullable=true)
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
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
}
