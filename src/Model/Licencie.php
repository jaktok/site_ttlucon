<?php

namespace App\Model;


use App\Entity\Classement;
use App\Entity\Categories;
use Symfony\Component\Validator\Constraints as Assert;

class Licencie
{
    private $id;
    /**
     * @Assert\NotNull
     */
    private $nom;
    /**
     * @Assert\NotNull
    */
    private $prenom;
    private $mail;
    private $telephone;
    private $adresse;
    private $cp;
    private $ville;
    private $certificat;
    private $cotisation;
    private $divers;
    private $bureau;
    private $actif;
    private $num_licence;
    private $date_naissance;
    private $nom_photo;
    private $date_certificat;
    private $indiv;
    private $contact_nom;
    private $contact_prenom;
    private $contact_tel;
    /**
    * @Assert\Type(type="integer")
    * @Assert\NotNull
    */
    private $classement;
    private $categories;
    private $libelleCat;
    private $photo;
    private $nbJoursCertif;
    

    

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

    public function getClassement(): ?int
    {
        return $this->classement;
    }
    
    public function setClassement(int $classement): self
    {
        $this->classement = $classement;
        
        return $this;
    }
    /**
     * @return mixed
     */
    public function getCategories(): Categories
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories(Categories $categories)
    {
        $this->categories = $categories;
    }
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return mixed
     */
    public function getLibelleCat()
    {
        return $this->libelleCat;
    }

    /**
     * @param mixed $libelleCat
     */
    public function setLibelleCat($libelleCat)
    {
        $this->libelleCat = $libelleCat;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }
    /**
     * @return mixed
     */
    public function getActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif(bool $actif)
    {
        $this->actif = $actif;
    }
    /**
     * @return mixed
     */
    public function getNbJoursCertif()
    {
        return $this->nbJoursCertif;
    }

    /**
     * @param mixed $nbJoursCertif
     */
    public function setNbJoursCertif($nbJoursCertif)
    {
        $this->nbJoursCertif = $nbJoursCertif;
    }



}