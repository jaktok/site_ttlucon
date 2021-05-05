<?php

namespace App\Entity;

use App\Repository\EquipeRencontreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipeRencontreRepository::class)
 */
class EquipeRencontre
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
    private $capitaine;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $saison;

    /**
     * @ORM\ManyToMany(targetEntity=Joueurs::class, inversedBy="equipeRencontres")
     */
    private $joueur;

    /**
     * @ORM\OneToOne(targetEntity=Fichiers::class, inversedBy="equipeRencontre", cascade={"persist", "remove"})
     */
    private $fichier;

    /**
     * @ORM\OneToOne(targetEntity=Rencontres::class, mappedBy="equipeRencontre", cascade={"persist", "remove"})
     */
    private $rencontre;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="equipeRencontre")
     */
    private $categories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero;

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

    public function getCapitaine(): ?int
    {
        return $this->capitaine;
    }

    public function setCapitaine(?int $capitaine): self
    {
        $this->capitaine = $capitaine;

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

    public function getFichier(): ?Fichiers
    {
        return $this->fichier;
    }

    public function setFichier(?Fichiers $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getRencontre(): ?Rencontres
    {
        return $this->rencontre;
    }

    public function setRencontre(?Rencontres $rencontre): self
    {
        // unset the owning side of the relation if necessary
        if ($rencontre === null && $this->rencontre !== null) {
            $this->rencontre->setEquipeRencontre(null);
        }

        // set the owning side of the relation if necessary
        if ($rencontre !== null && $rencontre->getEquipeRencontre() !== $this) {
            $rencontre->setEquipeRencontre($this);
        }

        $this->rencontre = $rencontre;

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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }
}
