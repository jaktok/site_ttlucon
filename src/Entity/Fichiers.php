<?php

namespace App\Entity;

use App\Repository\FichiersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichiersRepository::class)
 */
class Fichiers
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
     * @ORM\Column(type="string", length=150)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $format;

    /**
     * @ORM\OneToOne(targetEntity=Joueurs::class, inversedBy="photo")
     */
    private $joueur;

    /**
     * @ORM\OneToOne(targetEntity=EquipeType::class, inversedBy="photo", cascade={"persist", "remove"})
     */
    private $equipeType;

    /**
     * @ORM\OneToOne(targetEntity=EquipeRencontre::class, mappedBy="fichier", cascade={"persist", "remove"})
     */
    private $equipeRencontre;

    /**
     * @ORM\ManyToOne(targetEntity=Articles::class, inversedBy="fichier",cascade={"persist"})
     */
    private $articles;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

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

    public function getEquipeType(): ?EquipeType
    {
        return $this->equipeType;
    }

    public function setEquipeType(?EquipeType $equipeType): self
    {
        $this->equipeType = $equipeType;

        return $this;
    }

    public function getEquipeRencontre(): ?EquipeRencontre
    {
        return $this->equipeRencontre;
    }

    public function setEquipeRencontre(?EquipeRencontre $equipeRencontre): self
    {
        // unset the owning side of the relation if necessary
        if ($equipeRencontre === null && $this->equipeRencontre !== null) {
            $this->equipeRencontre->setFichier(null);
        }

        // set the owning side of the relation if necessary
        if ($equipeRencontre !== null && $equipeRencontre->getFichier() !== $this) {
            $equipeRencontre->setFichier($this);
        }

        $this->equipeRencontre = $equipeRencontre;

        return $this;
    }

    public function getArticles(): ?Articles
    {
        return $this->articles;
    }

    public function setArticles(?Articles $articles): self
    {
        $this->articles = $articles;

        return $this;
    }
}
