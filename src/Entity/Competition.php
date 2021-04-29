<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompetitionRepository::class)
 */
class Competition
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $num_journee;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity=Joueurs::class, inversedBy="competition_joueur")
     */
    private $joueur_compet;

    /**
     * @ORM\ManyToOne(targetEntity=TypeCompetition::class, inversedBy="competition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type_competition;

    public function __construct()
    {
        $this->joueur_compet = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNumJournee(): ?int
    {
        return $this->num_journee;
    }

    public function setNumJournee(?int $num_journee): self
    {
        $this->num_journee = $num_journee;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Joueurs[]
     */
    public function getJoueurCompet(): Collection
    {
        return $this->joueur_compet;
    }

    public function addJoueurCompet(Joueurs $joueurCompet): self
    {
        if (!$this->joueur_compet->contains($joueurCompet)) {
            $this->joueur_compet[] = $joueurCompet;
        }

        return $this;
    }

    public function removeJoueurCompet(Joueurs $joueurCompet): self
    {
        $this->joueur_compet->removeElement($joueurCompet);

        return $this;
    }

    public function getTypeCompetition(): ?TypeCompetition
    {
        return $this->type_competition;
    }

    public function setTypeCompetition(?TypeCompetition $type_competition): self
    {
        $this->type_competition = $type_competition;

        return $this;
    }
}
