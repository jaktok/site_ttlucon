<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 */
class Categories
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=Joueurs::class, inversedBy="categories")
     */
    private $joueur;

    /**
     * @ORM\OneToMany(targetEntity=EquipeRencontre::class, mappedBy="categories")
     */
    private $equipeRencontre;

    /**
     * @ORM\OneToMany(targetEntity=EquipeType::class, mappedBy="categories")
     */
    private $equipeType;

    /**
     * @ORM\OneToMany(targetEntity=Entrainement::class, mappedBy="categorie")
     */
    private $entrainements;

    public function __construct()
    {
        $this->joueur = new ArrayCollection();
        $this->equipeRencontre = new ArrayCollection();
        $this->equipeType = new ArrayCollection();
        $this->entrainements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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

    /**
     * @return Collection|EquipeRencontre[]
     */
    public function getEquipeRencontre(): Collection
    {
        return $this->equipeRencontre;
    }

    public function addEquipeRencontre(EquipeRencontre $equipeRencontre): self
    {
        if (!$this->equipeRencontre->contains($equipeRencontre)) {
            $this->equipeRencontre[] = $equipeRencontre;
            $equipeRencontre->setCategories($this);
        }

        return $this;
    }

    public function removeEquipeRencontre(EquipeRencontre $equipeRencontre): self
    {
        if ($this->equipeRencontre->removeElement($equipeRencontre)) {
            // set the owning side to null (unless already changed)
            if ($equipeRencontre->getCategories() === $this) {
                $equipeRencontre->setCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EquipeType[]
     */
    public function getEquipeType(): Collection
    {
        return $this->equipeType;
    }

    public function addEquipeType(EquipeType $equipeType): self
    {
        if (!$this->equipeType->contains($equipeType)) {
            $this->equipeType[] = $equipeType;
            $equipeType->setCategories($this);
        }

        return $this;
    }

    public function removeEquipeType(EquipeType $equipeType): self
    {
        if ($this->equipeType->removeElement($equipeType)) {
            // set the owning side to null (unless already changed)
            if ($equipeType->getCategories() === $this) {
                $equipeType->setCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Entrainement[]
     */
    public function getEntrainements(): Collection
    {
        return $this->entrainements;
    }

    public function addEntrainement(Entrainement $entrainement): self
    {
        if (!$this->entrainements->contains($entrainement)) {
            $this->entrainements[] = $entrainement;
            $entrainement->setCategorie($this);
        }

        return $this;
    }

    public function removeEntrainement(Entrainement $entrainement): self
    {
        if ($this->entrainements->removeElement($entrainement)) {
            // set the owning side to null (unless already changed)
            if ($entrainement->getCategorie() === $this) {
                $entrainement->setCategorie(null);
            }
        }

        return $this;
    }
}
