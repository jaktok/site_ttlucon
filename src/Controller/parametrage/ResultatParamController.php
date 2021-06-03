<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RencontresRepository;
use App\Repository\MatchsRepository;
use App\Entity\Rencontres;
use App\Entity\Matchs;
use App\Form\ResultatsType;
use App\Form\MatchsType;
use FFTTApi\FFTTApi;

class ResultatParamController extends AbstractController
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
     * @Route("/capitaine/param/resultat", name="resultat_param")
     */
    public function listRencontreResultat(RencontresRepository $rencontreRepo): Response
    {
        return $this->render('parametrage/resultat_param/resultat_param.html.twig', [
            'resultats' => $rencontreRepo->findAll(),
        ]);
    }

    /**
     * @Route("/capitaine/param/resultat/modifier/{id}", name="modifier_resultat_param")
     */
    public function Resultat(Request $request,RencontresRepository $rencontreRepo,Rencontres $rencontre): Response
    {
        if(!$rencontre)
        {
            $rencontre = new Rencontres();
        }
        if($rencontre){
            if($rencontre->getDomicile() == true){
                $nomAdversaire = $rencontre->getequipeB();
                $nomTTL = $rencontre->getequipeA();
                $lettreTTL = 'A';
                //dd($nomTTL);
            }
            else{
                $nomAdversaire = $rencontre->getequipeA();
                $nomTTL = $rencontre->getequipeB();
                $lettreTTL = 'B';
                //dd($nomAdversaire);
            }
            $nomClubAdversaire = substr($nomAdversaire,0,-2);
            $clubByName = $this->api->getClubsByName($nomClubAdversaire);
            $idAdversaire = $clubByName[0]->getNumero();
            //dd($idAdversaire);
            $detailRencontreByLien = $this->api->getDetailsRencontreByLien($rencontre->getIsRetour(),$this->idClub,$idAdversaire);
            //dd($detailRencontreByLien);
            //$p = $detailRencontreByLien->getParties()[0]->getAdversaireA();
            //dd($p);
            $partieRencontre = $detailRencontreByLien->getParties();
            //dd($partieRencontre);
            foreach ($partieRencontre as $partie) 
            {
                if($partie->getAdversaireA() != 'double' || $partie->getAdversaireB() != 'double')
                {
                    //dd($partie);
                }
                
            }
        }
       
        $form = $this->createForm(ResultatsType::class, $rencontre);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rencontre);
            $entityManager->flush();

            return $this->redirectToRoute('resultat_param');
        }

        
        return $this->render('parametrage/resultat_param/fiche_resultat_param.html.twig', [
            'formResultat' => $form->createView(),
            'rencontre' => $rencontre
        ]);
    }

    /**
     * @Route("/capitaine/param/resultat/double/{id}", name="double_resultat_param")
     */
    public function doubleResultat(Request $request,int $id = null,MatchsRepository $matchRepo): Response
    {
        $matchs = $matchRepo->findByIdRencontre($id);
        //dd($matchs);
        //return $this->redirectToRoute('resultat_param');

        return $this->render('parametrage/resultat_param/double_param.html.twig', [
            'matchs' => $matchs
        ]);
    }

    /**
     * @Route("/capitaine/param/resultat/new", name="new_double_resultat_param")
     * @Route("/capitaine/param/resultat/double/modifer/{id}", name="modifier_double_resultat_param")
     */
    public function doublemodifResultat(Request $request,MatchsRepository $matchRepo, Matchs $match = null): Response
    {
        if(!$match){
            $match = new Matchs();
        }

        $form = $this->createForm(MatchsType::class, $match);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $match->setMatchDouble(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('resultat_param');
        }

        return $this->render('parametrage/resultat_param/fiche_double_param.html.twig', [
            'formMatch' => $form->createView(),
        ]);
    }
}
