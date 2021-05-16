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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $set_gagne;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $set_perdu;

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

    public function getSetGagne(): ?int
    {
        return $this->set_gagne;
    }

    public function setSetGagne(?int $set_gagne): self
    {
        $this->set_gagne = $set_gagne;

        return $this;
    }

    public function getSetPerdu(): ?int
    {
        return $this->set_perdu;
    }

    public function setSetPerdu(?int $set_perdu): self
    {
        $this->set_perdu = $set_perdu;

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
}
