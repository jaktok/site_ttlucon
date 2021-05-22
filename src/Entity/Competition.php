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
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="competition")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="competition")
     */
    private $matchs;

    public function __construct()
    {
        $this->joueur_compet = new ArrayCollection();
        $this->matchs = new ArrayCollection();
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

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

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
            $match->setCompetition($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): self
    {
        if ($this->matchs->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getCompetition() === $this) {
                $match->setCompetition(null);
            }
        }

        return $this;
    }
}
