<?php

namespace App\Entity;

use App\Repository\RencontresRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RencontresRepository::class)
 */
class Rencontres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_rencontre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $adversaire;

    /**
     * @ORM\Column(type="boolean")
     */
    private $domicile;

    /**
     * @ORM\Column(type="integer")
     */
    private $no_journee;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $victoire;

    /**
     * @ORM\OneToOne(targetEntity=EquipeRencontre::class, inversedBy="rencontre", cascade={"persist", "remove"})
     */
    private $equipeRencontre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRencontre(): ?\DateTimeInterface
    {
        return $this->date_rencontre;
    }

    public function setDateRencontre(\DateTimeInterface $date_rencontre): self
    {
        $this->date_rencontre = $date_rencontre;

        return $this;
    }

    public function getAdversaire(): ?string
    {
        return $this->adversaire;
    }

    public function setAdversaire(string $adversaire): self
    {
        $this->adversaire = $adversaire;

        return $this;
    }

    public function getDomicile(): ?bool
    {
        return $this->domicile;
    }

    public function setDomicile(bool $domicile): self
    {
        $this->domicile = $domicile;

        return $this;
    }

    public function getNoJournee(): ?int
    {
        return $this->no_journee;
    }

    public function setNoJournee(int $no_journee): self
    {
        $this->no_journee = $no_journee;

        return $this;
    }

    public function getVictoire(): ?bool
    {
        return $this->victoire;
    }

    public function setVictoire(?bool $victoire): self
    {
        $this->victoire = $victoire;

        return $this;
    }

    public function getEquipeRencontre(): ?EquipeRencontre
    {
        return $this->equipeRencontre;
    }

    public function setEquipeRencontre(?EquipeRencontre $equipeRencontre): self
    {
        $this->equipeRencontre = $equipeRencontre;

        return $this;
    }
}
