<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RencontresRepository;
use App\Repository\FichiersRepository;
use App\Repository\EquipeTypeRepository;
use FFTTApi\FFTTApi;
use phpDocumentor\Reflection\Types\Array_;

class ResultatController extends AbstractController
{
    private $ini_array;
    private $api;
    private $idClub;
    private $racineLien;
    
    public function __construct()
    {
        // Recuperation infos config.ini
        $this->ini_array = parse_ini_file("../config/config.ini");
        $this->api = new FFTTApi();
        $this->idClub = $this->ini_array['id_club_lucon'];
        $this->racineLien = $this->ini_array['racine_lien_div'];
    }
    
    /**
     * @Route("/resultat/{cat}", name="resultat")
     */
    public function index(RencontresRepository $rencontreRepo,$cat=null,EquipeTypeRepository $equipeRepo): Response
    {
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
                break;
            default :
                $this->categorie = "Adulte";
        }

        // on récupère la phase
        $phase = 2;
        $moisEncours = date("m");
        $tabPhase1 = array("08","09","10","11","12");
        $tabPhase2 = array("01","02","03","04","05","06","07");
        if (in_array($moisEncours, $tabPhase1)){
            $phase = 1;
        }
        if (in_array($moisEncours, $tabPhase2)){
            $phase = 2;
        }
        
        $equipes = $equipeRepo->findBy(array(),array('nom' => 'ASC'));
        $tabClassement = array();
        
        $equipesByClub = $this->api->getEquipesByClub($this->idClub,"M",$phase);//dd($equipes);
       //dd($equipesByClub);
        $i=0;
        foreach ($equipes as $equipe){
            foreach ($equipesByClub as $equipeFFTT){
                    $tabNomEquipeFFTT = explode(" ", $equipeFFTT->getLibelle());
                    if(isset($tabNomEquipeFFTT[1])){
                    $nomEquipe = $tabNomEquipeFFTT[0]." ".$tabNomEquipeFFTT[1];//dd($nomEquipe);
                    if($nomEquipe == $equipe->getNom() && $equipe->getCategories()->getLibelle()==$this->categorie){
                        $lien =  $this->racineLien.$equipeFFTT->getLienDivision();
                       $tabClassement[$i]["lien"] = $lien;
                       $tabClassement[$i]["equipe"] = $nomEquipe;
                       $lienDivision = $equipeFFTT->getLienDivision();
                       $pouleByLien = $this->api->getClassementPouleByLienDivision($lienDivision);
                       $tabClassement[$i]["classement"] = $pouleByLien;
                       $i++;
                    }
                }
            }
        }
        
        $calendrierPhase1 = $rencontreRepo->findByPhase(1);//dd($calendrierPhase1);
        //dd($tabClassement);
        $calendrierPhase2 = $rencontreRepo->findByPhase(2);
        return $this->render('resultat/resultat.html.twig', [
            'resultats' => $rencontreRepo->findAll(),
            'categorie' => $this->categorie,
            'calendrierPhase1' => $calendrierPhase1,
            'calendrierPhase2' => $calendrierPhase2,
            'equipes' => $equipes,
            'tabClassement' => $tabClassement,
        ]);
    }

    /**
     * @Route("/resultat/rencontre/{id}", name="resultat_rencontre")
     */
    public function afficherRencontre(KernelInterface $kernelInterface,FichiersRepository $fichierRepo,RencontresRepository $rencontreRepo,int $id=null): Response
    {
        if($id){
            $rencontre = $rencontreRepo->find($id);
        }
        $fichier = $rencontre->getFichier();
        $nom = $fichier->getNom();
        $projetcRoot = $kernelInterface->getProjectDir();
            return new BinaryFileResponse($projetcRoot.'/public/resultats/'.$nom);
    }
}
