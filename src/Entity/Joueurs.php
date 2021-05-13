<?php

namespace App\Entity;

use App\Repository\JoueursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JoueursRepository::class)
 */
class Joueurs
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $certificat;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $cotisation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $divers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bureau;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $num_licence;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_naissance;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $nom_photo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_certificat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $indiv;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $contact_nom;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $contact_prenom;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $contact_tel;

    /**
     * @ORM\ManyToMany(targetEntity=Competition::class, mappedBy="joueur_compet")
     */
    private $competition_joueur;

    /**
     * @ORM\ManyToMany(targetEntity=EquipeType::class, mappedBy="joueur")
     */
    private $equipeTypes;

    /**
     * @ORM\OneToOne(targetEntity=Fichiers::class, mappedBy="joueur", cascade={"persist", "remove"})
     */
    private $photo;

    /**
     * @ORM\ManyToMany(targetEntity=EquipeRencontre::class, mappedBy="joueur")
     */
    private $equipeRencontres;

    /**
     * @ORM\ManyToMany(targetEntity=Categories::class, mappedBy="joueur")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Classement::class, mappedBy="joueur")
     */
    private $classements;

    /**
     * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="joueur")
     */
    private $matchs;

    /**
     * @ORM\OneToMany(targetEntity=Articles::class, mappedBy="joueur")
     */
    private $articles;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="joueur")
     */
    private $role;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, mappedBy="joueur", cascade={"persist", "remove"})
     */
    private $users;

    public function __construct()
    {
        $this->competition_joueur = new ArrayCollection();
        $this->equipeTypes = new ArrayCollection();
        $this->equipeRencontres = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->classements = new ArrayCollection();
        $this->matchs = new ArrayCollection();
        $this->articles = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCertificat(): ?bool
    {
        return $this->certificat;
    }

    public function setCertificat(?bool $certificat): self
    {
        $this->certificat = $certificat;

        return $this;
    }

    public function getCotisation(): ?bool
    {
        return $this->cotisation;
    }

    public function setCotisation(?bool $cotisation): self
    {
        $this->cotisation = $cotisation;

        return $this;
    }

    public function getDivers(): ?string
    {
        return $this->divers;
    }

    public function setDivers(?string $divers): self
    {
        $this->divers = $divers;

        return $this;
    }

    public function getBureau(): ?bool
    {
        return $this->bureau;
    }

    public function setBureau(bool $bureau): self
    {
        $this->bureau = $bureau;

        return $this;
    }

    public function getNumLicence(): ?string
    {
        return $this->num_licence;
    }

    public function setNumLicence(?string $num_licence): self
    {
        $this->num_licence = $num_licence;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getNomPhoto(): ?string
    {
        return $this->nom_photo;
    }

    public function setNomPhoto(?string $nom_photo): self
    {
        $this->nom_photo = $nom_photo;

        return $this;
    }

    public function getDateCertificat(): ?\DateTimeInterface
    {
        return $this->date_certificat;
    }

    public function setDateCertificat(?\DateTimeInterface $date_certificat): self
    {
        $this->date_certificat = $date_certificat;

        return $this;
    }

    public function getIndiv(): ?bool
    {
        return $this->indiv;
    }

    public function setIndiv(bool $indiv): self
    {
        $this->indiv = $indiv;

        return $this;
    }

    public function getContactNom(): ?string
    {
        return $this->contact_nom;
    }

    public function setContactNom(?string $contact_nom): self
    {
        $this->contact_nom = $contact_nom;

        return $this;
    }

    public function getContactPrenom(): ?string
    {
        return $this->contact_prenom;
    }

    public function setContactPrenom(?string $contact_prenom): self
    {
        $this->contact_prenom = $contact_prenom;

        return $this;
    }

    public function getContactTel(): ?string
    {
        return $this->contact_tel;
    }

    public function setContactTel(?string $contact_tel): self
    {
        $this->contact_tel = $contact_tel;

        return $this;
    }

    /**
     * @return Collection|Competition[]
     */
    public function getCompetitionJoueur(): Collection
    {
        return $this->competition_joueur;
    }

    public function addCompetitionJoueur(Competition $competitionJoueur): self
    {
        if (!$this->competition_joueur->contains($competitionJoueur)) {
            $this->competition_joueur[] = $competitionJoueur;
            $competitionJoueur->addJoueurCompet($this);
        }

        return $this;
    }

    public function removeCompetitionJoueur(Competition $competitionJoueur): self
    {
        if ($this->competition_joueur->removeElement($competitionJoueur)) {
            $competitionJoueur->removeJoueurCompet($this);
        }

        return $this;
    }

    /**
     * @return Collection|EquipeType[]
     */
    public function getEquipeTypes(): Collection
    {
        return $this->equipeTypes;
    }

    public function addEquipeType(EquipeType $equipeType): self
    {
        if (!$this->equipeTypes->contains($equipeType)) {
            $this->equipeTypes[] = $equipeType;
            $equipeType->addJoueur($this);
        }

        return $this;
    }

    public function removeEquipeType(EquipeType $equipeType): self
    {
        if ($this->equipeTypes->removeElement($equipeType)) {
            $equipeType->removeJoueur($this);
        }

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
            $this->photo->setJoueur(null);
        }

        // set the owning side of the relation if necessary
        if ($photo !== null && $photo->getJoueur() !== $this) {
            $photo->setJoueur($this);
        }

        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|EquipeRencontre[]
     */
    public function getEquipeRencontres(): Collection
    {
        return $this->equipeRencontres;
    }

    public function addEquipeRencontre(EquipeRencontre $equipeRencontre): self
    {
        if (!$this->equipeRencontres->contains($equipeRencontre)) {
            $this->equipeRencontres[] = $equipeRencontre;
            $equipeRencontre->addJoueur($this);
        }

        return $this;
    }

    public function removeEquipeRencontre(EquipeRencontre $equipeRencontre): self
    {
        if ($this->equipeRencontres->removeElement($equipeRencontre)) {
            $equipeRencontre->removeJoueur($this);
        }

        return $this;
    }

    /**
     * @return Collection|Categories[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categories $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addJoueur($this);
        }

        return $this;
    }

    public function removeCategory(Categories $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeJoueur($this);
        }

        return $this;
    }

    /**
     * @return Collection|Classement[]
     */
    public function getClassements(): Collection
    {
        return $this->classements;
    }

    public function addClassement(Classement $classement): self
    {
        if (!$this->classements->contains($classement)) {
            $this->classements[] = $classement;
            $classement->setJoueur($this);
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->removeElement($classement)) {
            // set the owning side to null (unless already changed)
            if ($classement->getJoueur() === $this) {
                $classement->setJoueur(null);
            }
        }

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
            $match->setJoueur($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): self
    {
        if ($this->matchs->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getJoueur() === $this) {
                $match->setJoueur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setJoueur($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getJoueur() === $this) {
                $article->setJoueur(null);
            }
        }

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }


    public function setUsers(?Users $users) : self
    {
        // unset the owning side of the relation if necessary
        if ($users === null && $this->users !== null) {
            $this->users->setJoueur(null);
        }

        // set the owning side of the relation if necessary
        if ($users !== null && $users->getJoueur() !== $this) {
            $users->setJoueur($this);
        }

        $this->users = $users;

        return $this;
    }
}
