<?php
/**
 * Created by Antoine Lamirault.
 */

namespace FFTTApi\Model;


class EquipePoule
{
    private $classement;
    private $nomEquipe;
    private $matchJouees;
    private $points;
    private $numero;
    private $victoires;
    private $defaites;
    private $idEquipe;
    private $idCLub;
    private $nbVic;
    private $nbDef;
    private $nbNull;
    private $nbForfaits;
    

    public function __construct(
        int $classement,
        string $nomEquipe,
        int $matchJouees,
        int $points,
        string $numero,
        int $victoires,
        int $defaites,
        int $idEquipe,
        string $idCLub,
        int $nbVic,
        int $nbDef,
        int $nbNull,
        int $nbForfaits
        )
    {
        $this->classement = $classement;
        $this->nomEquipe = $nomEquipe;
        $this->matchJouees = $matchJouees;
        $this->points = $points;
        $this->numero = $numero;
        $this->victoires = $victoires;
        $this->defaites = $defaites;
        $this->idEquipe = $idEquipe;
        $this->idCLub = $idCLub;
        $this->nbVic = $nbVic;
        $this->nbDef = $nbDef;
        $this->nbNull = $nbNull;
        $this->nbForfaits = $nbForfaits;
        
    }

    /**
     * @return mixed
     */
    public function getNbVic()
    {
        return $this->nbVic;
    }

    /**
     * @return mixed
     */
    public function getNbDef()
    {
        return $this->nbDef;
    }

    /**
     * @return mixed
     */
    public function getNbNull()
    {
        return $this->nbNull;
    }

    /**
     * @return mixed
     */
    public function getNbForfaits()
    {
        return $this->nbForfaits;
    }

    public function getClassement(): int
    {
        return $this->classement;
    }

    public function getNomEquipe(): string
    {
        return $this->nomEquipe;
    }

    public function getMatchJouees(): int
    {
        return $this->matchJouees;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function getVictoires(): int
    {
        return $this->victoires;
    }

    public function getDefaites(): int
    {
        return $this->defaites;
    }

    public function getIdEquipe(): int
    {
        return $this->idEquipe;
    }

    public function getIdCLub(): string
    {
        return $this->idCLub;
    }
}