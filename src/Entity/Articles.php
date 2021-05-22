<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticlesRepository::class)
 */
class Articles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $auteur;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $en_ligne;

    /**
     * @ORM\ManyToOne(targetEntity=Joueurs::class, inversedBy="articles")
     */
    private $joueur;

    /**
     * @ORM\OneToMany(targetEntity=Fichiers::class, mappedBy="articles")
     */
    private $fichier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    public function __construct()
    {
        $this->fichier = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEnLigne(): ?bool
    {
        return $this->en_ligne;
    }

    public function setEnLigne(?bool $en_ligne): self
    {
        $this->en_ligne = $en_ligne;

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

    /**
     * @return Collection|Fichiers[]
     */
    public function getFichier(): Collection
    {
        return $this->fichier;
    }

    public function addFichier(Fichiers $fichier): self
    {
        if (!$this->fichier->contains($fichier)) {
            $this->fichier[] = $fichier;
            $fichier->setArticles($this);
        }

        return $this;
    }

    public function removeFichier(Fichiers $fichier): self
    {
        if ($this->fichier->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getArticles() === $this) {
                $fichier->setArticles(null);
            }
        }

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }
}
