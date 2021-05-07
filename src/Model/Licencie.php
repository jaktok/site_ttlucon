<?php

namespace App\Model;


use App\Entity\Classement;
use App\Entity\Role;

class Licencie
{
    private $id;
    private $nom;
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
    private $num_licence;
    private $date_naissance;
    private $nom_photo;
    private $date_certificat;
    private $indiv;
    private $contact_nom;
    private $contact_prenom;
    private $contact_tel;
    private $classement;
    private $role;

    
   /* public function __construct(string $id, string $nom, string $prenom, string $mail, string $telephone, string $adresse, string $cp, string $ville, bool $certificat, bool $cotisation, string $divers, bool $bureau, string $num_licence, \DateTimeInterface $date_naissance, string $nom_photo, \DateTimeInterface $date_certificat, bool $indiv, string $contact_nom, string $contact_prenom, string $contact_tel, string $classement, int $role ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->mail = $mail;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
        $this->cp = $cp;
        $this->ville = $ville;
        $this->certificat = $certificat;
        $this->cotisation = $cotisation;
        $this->divers = $divers;
        $this->bureau = $bureau;
        $this->num_licence = $num_licence;
        $this->date_naissance = $date_naissance;
        $this->nom_photo = $nom_photo;
        $this->date_certificat = $date_certificat;
        $this->indiv = $indiv;
        $this->contact_nom = $contact_nom;
        $this->contact_prenom = $contact_prenom;
        $this->contact_tel = $contact_tel;
        $this->classement = $classement;
        $this->role = $role;
    }*/
    

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
    
    /*public function getDateNaissance(): ?string
    {
        return $this->date_naissance;
    }
    
    public function setDateNaissance(?string $date_naissance): self
    {
        $this->date_naissance = $date_naissance;
        
        return $this;
    }*/

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
    
    /* public function getDateCertificat(): ?string
    {
        return $this->date_certificat;
    }
    
    public function setDateCertificat(string $date_certificat): self
    {
        $this->date_certificat = $date_certificat;
        
        return $this;
    }*/

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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    


}
