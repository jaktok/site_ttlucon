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
     * @ORM\ManyToOne(targetEntity=Articles::class, inversedBy="fichier",cascade={"persist"})
     */
    private $articles;

    /**
     * @ORM\OneToOne(targetEntity=DocAccueil::class, mappedBy="fichier", cascade={"persist", "remove"})
     */
    private $docAccueil;

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

    public function getArticles(): ?Articles
    {
        return $this->articles;
    }

    public function setArticles(?Articles $articles): self
    {
        $this->articles = $articles;

        return $this;
    }

    public function getDocAccueil(): ?DocAccueil
    {
        return $this->docAccueil;
    }

    public function setDocAccueil(?DocAccueil $docAccueil): self
    {
        // unset the owning side of the relation if necessary
        if ($docAccueil === null && $this->docAccueil !== null) {
            $this->docAccueil->setFichier(null);
        }

        // set the owning side of the relation if necessary
        if ($docAccueil !== null && $docAccueil->getFichier() !== $this) {
            $docAccueil->setFichier($this);
        }

        $this->docAccueil = $docAccueil;

        return $this;
    }
}
