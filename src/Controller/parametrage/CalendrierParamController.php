<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\Common\Persistence\ObjectManager;
use FFTTApi\FFTTApi;
use App\Entity\Rencontres;
use App\Form\RencontreType;
use App\Repository\RencontresRepository;
use App\Repository\EquipeTypeRepository;
use App\Repository\MatchsRepository;
use Symfony\Component\Validator\Constraints\DateTime;
class CalendrierParamController extends AbstractController
{
    private $ini_array;
    private $api;
    private $idClub;

    public function __construct()
    {
        // Recuperation infos config.ini
        $this->ini_array = parse_ini_file("../config/config.ini");
        $this->api = new FFTTApi();
        $this->idClub = $this->ini_array['id_club_lucon'];
    }

    /**
     * @Route("/dirigeant/param/calendrier/new/{idTeam}", name="calendrier_param_new")
     * @Route("/dirigeant/param/calendrier/modifier/{id}", name="calendrier_param_modif")
     * 
     */ 
    public function createCalendrier(Request $request,RencontresRepository $rencontreRepo, int $id = null, int $idTeam = null)
    {
        $rencontre = new Rencontres();
        $idEquipe= $idTeam ;
        if($id)
        {
            $rencontre = $rencontreRepo->find($id);
            $idEquipe = $rencontre->getEquipeType()->getId();
            $dateRencontre = $rencontre->getDateRencontre();
        }
        $dateRencontre = null;
        $form = $this->createForm(RencontreType::class, $rencontre);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $idEquipe = $form["equipeType"]->getData()->getId();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rencontre);
            $entityManager->flush();

            return $this->redirectToRoute('calendrier_param',array('id' => $idEquipe));
        }
        return $this->render('parametrage/calendrier_param/fiche_calendrier_param.html.twig', [
            'formRencontre' => $form->createView(),
            'idEquipe' => $idEquipe,
            'dateRencontre' => $dateRencontre,
        ]);
    }

    /**
     * @Route("/supprime/calendrier/{id}", name="supprime_calendrier")
     */
    public function supprimeCalendrier(Request $request, RencontresRepository  $rencontreRepo,MatchsRepository $matchRepo, int $id = null, int $idTeam=null): Response
    {
        $idEquipe= $idTeam ;
        if($id)
        {
            $rencontre = $rencontreRepo->find($id);
            $idEquipe = $rencontre->getEquipeType()->getId();
        }

        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $rencontre = $rencontreRepo->find($id);
        
        if ($rencontre) {
            $entityManager = $this->getDoctrine()->getManager();

            $listeMatchs = $matchRepo->findByIdRencontre($rencontre->getId());
            // on boucle sur les matchs pour suppression
            foreach ($listeMatchs as $match){
                $entityManager->remove($match);
                $entityManager->flush();
            }
            
            $entityManager->remove($rencontre);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('calendrier_param',array('id' => $idEquipe));
        
    }


    /**
     * @Route("/majauto/calendrier/{idTeam}", name="majAuto_calendrier")
     */
    public function majAutoCalendrier(Request $request,RencontresRepository  $rencontreRepo, EquipeTypeRepository $equipeTypeRepo, int $id = null, int $idTeam=null): Response
    {
        $equipesByClub = $this->api->getEquipesByClub($this->idClub,"M");//dd($equipesByClub);
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($equipesByClub as $equipeClub) {
            $nom = $equipeClub->getLibelle();
            $tabNom = explode(" ",$nom);
            if ($tabNom[0]!=""){
            $nomFinal = $tabNom[0] . " " . $tabNom[1];
            $phase =  $tabNom[4];
            }
            else{
                $nomFinal = "EXEMPT";
                $phase = 1;
            }
            $equipeLucon = $equipeTypeRepo->findOneByNom($nomFinal);
            //dd($equipeLucon);
            if($equipeLucon && $equipeClub->getLienDivision()!=""){
                if($equipeLucon->getId() == $idTeam){
                    //dd($equipeClub->getLienDivision());
                    $rencontrePouleByLienDivR = $this->api->getRencontrePouleByLienDivision($equipeClub->getLienDivision());
                    //dd($rencontrePouleByLienDivR);
                    $noJournee = 1;
                    foreach($rencontrePouleByLienDivR as $rencontre) {
                        //dd($rencontre,$nomFinal);
                       // if($rencontre->getNomEquipeA() == "LUCON 1" || $rencontre->getNomEquipeB() == "LUCON 1"){ dd($rencontre,$nomFinal);}
                        //$date = substr($rencontre->getLibelle(),-10);
                        // formatage d ela date pour pouvoir comparer si elle existe
                        $date = $rencontre->getDateReelle()." 00:00:00";
                        $dateTime = str_replace("/", "-", $date);
                        $dt = new \DateTime($dateTime, new \DateTimeZone('Europe/Paris'));//dd($dateee->format("d-m-y H:i:s"));
                        $dt->format("d-m-y H:i:s");
                        //$date = new DateTime("2012-09-01 12:00:00");
                        //$dt = \DateTime::createFromFormat("d-m-y H:i:s", $dateTime);
                        if($rencontre->getNomEquipeA() == $nomFinal || $rencontre->getNomEquipeB() == $nomFinal){
                           // dd($rencontre->getNomEquipeA(),$rencontre->getNomEquipeB(), $nomFinal);
                            $Newrencontre = new Rencontres();
                            if($nomFinal == $rencontre->getNomEquipeA()){
                                $Newrencontre->setDomicile(true);
                            }
                            else{
                                $Newrencontre->setDomicile(false);
                            }
                            if ($rencontre->getNomEquipeA()!=""){
                                $Newrencontre->setequipeA($rencontre->getNomEquipeA());
                            }
                            else{
                                $Newrencontre->setequipeA("EXEMPT");
                            }
                            if ($rencontre->getNomEquipeB()!=""){
                                $Newrencontre->setequipeB($rencontre->getNomEquipeB());
                            }
                            else{
                                $Newrencontre->setequipeB("EXEMPT");
                            }
                            
                            $Newrencontre->setDateRencontre($dt);
                            $Newrencontre->setNoJournee($noJournee);
                            $Newrencontre->setIsRetour($rencontre->getLien());
                            $Newrencontre->setPhase($phase);
                            $Newrencontre->setEquipeType($equipeLucon);
                            $existeRencontre = $rencontreRepo->findByNomEquipeDate($Newrencontre->getEquipeA(),$dt->format("Y-m-d")." 00:00:00");
                            //dd($existeRencontre,$Newrencontre->getEquipeA(),$dt->format("Y-m-d")." 00:00:00",$dt);
                            if ($existeRencontre == null){
                            $entityManager->persist($Newrencontre);
                            $entityManager->flush();
                            }
                            $noJournee++;

                            
                        }
                    }
                }
            }
            //dd($equipeLucon);
            
        }  
        return $this->redirectToRoute('calendrier_param',array('id' => $idTeam));      
    }

    /**
     * @Route("/vider/calendrier/{idTeam}", name="vider_calendrier")
     */
    public function viderCalendrier(Request $request, RencontresRepository  $rencontreRepo,MatchsRepository $matchRepo, int $id = null, int $idTeam=null): Response
    {
        $listeRencontre = $rencontreRepo->findByEquipe($idTeam);
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($listeRencontre as $rencontre) {
            $listeMatchs = $matchRepo->findByIdRencontre($rencontre->getId());
            // on boucle sur les matchs pour suppression
            foreach ($listeMatchs as $match){
                $entityManager->remove($match);
                $entityManager->flush();
            }
            $entityManager->remove($rencontre);
            $entityManager->flush();
        }
        //dd($rencontre);

        return $this->redirectToRoute('calendrier_param',array('id' => $idTeam));
        
    }

    /**
     * @Route("/dirigeant/param/calendrier/{id}", name="calendrier_param")
     */ 
    public function index(RencontresRepository $rencontres, int $id = null): Response
    {
        
        $rencontre = $rencontres->findByEquipeByPhase($id,1);
        
        $rencontre2 = $rencontres->findByEquipeByPhase($id,2);
        
        return $this->render('parametrage/calendrier_param/calendrier_param.html.twig', [
            'rencontres' => $rencontre,
            'rencontres2' => $rencontre2,
            'idTeam' => $id,
        ]);
    }
}
