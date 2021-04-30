<?php

namespace App\Entity;

use App\Repository\EquipeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipeTypeRepository::class)
 */
class EquipeType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $num_equipe;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $division;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $saison;

    /**
     * @ORM\ManyToMany(targetEntity=Joueurs::class, inversedBy="equipeTypes")
     */
    private $joueur;

    /**
     * @ORM\OneToOne(targetEntity=Fichiers::class, mappedBy="equipeType", cascade={"persist", "remove"})
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="equipeType")
     */
    private $categories;

    public function __construct()
    {
        $this->joueur = new ArrayCollection();
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

    public function getNumEquipe(): ?int
    {
        return $this->num_equipe;
    }

    public function setNumEquipe(int $num_equipe): self
    {
        $this->num_equipe = $num_equipe;

        return $this;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(string $division): self
    {
        $this->division = $division;

        return $this;
    }

    public function getSaison(): ?string
    {
        return $this->saison;
    }

    public function setSaison(?string $saison): self
    {
        $this->saison = $saison;

        return $this;
    }

    /**
     * @return Collection|Joueurs[]
     */
    public function getJoueur(): Collection
    {
        return $this->joueur;
    }

    public function addJoueur(Joueurs $joueur): self
    {
        if (!$this->joueur->contains($joueur)) {
            $this->joueur[] = $joueur;
        }

        return $this;
    }

    public function removeJoueur(Joueurs $joueur): self
    {
        $this->joueur->removeElement($joueur);

        return $this;
    }

    public function getPhoto(): ?Fichiers
    {
        return $this->photo;
    }

    public function setPhoto(?Fichiers $photo): self
    {
        // unset the owning side of the relation if necessary
        if ($photo === null && $this->photo !== null) {
            $this->photo->setEquipeType(null);
        }

        // set the owning side of the relation if necessary
        if ($photo !== null && $photo->getEquipeType() !== $this) {
            $photo->setEquipeType($this);
        }

        $this->photo = $photo;

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
}
