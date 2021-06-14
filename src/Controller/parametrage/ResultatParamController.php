<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RencontresRepository;
use App\Repository\MatchsRepository;
use App\Repository\FichiersRepository;
use App\Repository\EquipeTypeRepository;
use App\Repository\JoueursRepository;
use App\Entity\Rencontres;
use App\Entity\Matchs;
use App\Entity\Fichiers;
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
    public function listRencontreResultat(RencontresRepository $rencontreRepo,EquipeTypeRepository $equipeRepo): Response
    {
        return $this->render('parametrage/resultat_param/resultat_param.html.twig', [
            'resultats' => $rencontreRepo->findAll(),
            'equipes' => $equipeRepo->findAll()
        ]);
    }

    /**
     * @Route("/capitaine/param/resultat/modifier/{id}", name="modifier_resultat_param")
     */
    public function Resultat(Request $request,FichiersRepository $ficRepo,MatchsRepository $matchRepo,RencontresRepository $rencontreRepo,Rencontres $rencontre): Response
    {
        if(!$rencontre)
        {
            $rencontre = new Rencontres();
        }

        $form = $this->createForm(ResultatsType::class, $rencontre);
        
        $form->handleRequest($request);

        $matchs = $matchRepo->findByIdRencontre($rencontre->getId());
        $idRencontre = $rencontre->getId();
        // Formatage des noms d'equipe pour le nom du fichier pdf
        $equipeA = $rencontre->getEquipeA();
        $equipeB = $rencontre->getEquipeB();
        $nomA = explode(" ",$equipeA);
        $nomB = explode(" ",$equipeB);
        $nomEquipeA = $nomA[0] . '-' . $nomA[1];
        $nomEquipeB = $nomB[0] . '-' . $nomB[1];

        //dd($matchs);
        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('fichier')->getData();
            //dd($images);
            if ($images){
                $fichier = $rencontre->getDateRencontre()->format('d-m-y').'-'.$nomEquipeA.'-'.$nomEquipeB.'.'.$images->guessExtension();
                // On copie le fichier dans le dossier uploads
                $images->move(
                    $this->getParameter('resultats_destination'),
                    $fichier
                    );
            }
            $img = new Fichiers();
            $entityManager = $this->getDoctrine()->getManager();
            if ($rencontre->getId()!=null && !(isset($fichier))){
               // $img = $ficRepo->findOneByRencontre($rencontre->getId());
                $img = $rencontre->getFichier();
            }
            if ($images && $img!=null&&$img->getId()!=null) {
                $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
                $image->setNom($fichier);
                $image->setUrl($this->getParameter('resultats_destination'));
                $entityManager->flush();
            }
            if (($img==null || $img->getId()==null) && isset($fichier)) {
                // On cr�e l'image dans la base de donn�es
                $img = new Fichiers();
                $img->setNom($fichier);
                $img->setRencontres($rencontre);
                //dd($img);
                $img->setUrl($this->getParameter('resultats_destination'));
                //dd($img);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($img);
                $entityManager->flush();
            }

            $scoreA = $form["scoreA"]->getData();
            $scoreB = $form["scoreB"]->getData();
            $domicile = $rencontre->getDomicile();
            if($scoreA > $scoreB && $domicile == true){
                $rencontre->setVictoire(true);
            }
            elseif($scoreA > $scoreB && $domicile == false){
                $rencontre->setVictoire(false);
            }
            elseif($scoreB > $scoreA && $domicile == true){
                $rencontre->setVictoire(false);
            }
            elseif($scoreB > $scoreA && $domicile == false){
                $rencontre->setVictoire(true);
            }
            else{
                $rencontre->setVictoire(null);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rencontre);
            $entityManager->flush();

            return $this->redirectToRoute('resultat_param');
        }

        
        return $this->render('parametrage/resultat_param/fiche_resultat_param.html.twig', [
            'formResultat' => $form->createView(),
            'rencontre' => $rencontre,
            'matchs' => $matchs,
            'idRencontre' => $idRencontre
        ]);
    }


    /**
     * @Route("/capitaine/param/resultat/match/new/{idRencontre}", name="new_match_resultat_param")
     * @Route("/capitaine/param/resultat/match/modifer/{id}", name="modifier_double_resultat_param")
     */
    public function doublemodifResultat(Request $request,RencontresRepository $rencontreRepo,MatchsRepository $matchRepo, Matchs $match = null,int $idRencontre = null, int $id = null): Response
    {
        $idMatch = $idRencontre;

        if(!$match){
            $match = new Matchs();
        }
        $form = $this->createForm(MatchsType::class, $match);
        
        $form->handleRequest($request);
        $idMatch = $_GET['id'];
        $rencontre = $rencontreRepo->find($idMatch);
        if($form->isSubmitted() && $form->isValid()){
            
            $match->setRencontre($rencontre);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('resultat_param');
        }

        return $this->render('parametrage/resultat_param/fiche_double_param.html.twig', [
            'formMatch' => $form->createView(),
            'idRencontre' => $idMatch,
        ]);
    }
    /**
     * @Route("/capitaine/param/resultat/auto/{id}", name="auto_resultat_param")
     */
    public function majAutoMatch(Request $request,int $id = null,RencontresRepository $rencontreRepo,MatchsRepository $matchRepo, JoueursRepository $joueursRepo): Response
    {
        $id = 78;
        $idRencontre = $id;
        $rencontre = $rencontreRepo->find($idRencontre);
        //dd($rencontre);
        if($rencontre)
        {
            if($rencontre->getDomicile() == true)
            {
                $nomAdversaire = $rencontre->getequipeB();
                $nomTTL = $rencontre->getequipeA();
                $lettreTTL = 'A';
                //dd($nomTTL);
            }
            else
            {
                $nomAdversaire = $rencontre->getequipeA();
                $nomTTL = $rencontre->getequipeB();
                $lettreTTL = 'B';
                //dd($nomAdversaire);
            }
            $nomClubAdversaire = substr($nomAdversaire,0,-2);
            $clubByName = $this->api->getClubsByName($nomClubAdversaire);
            //dd($clubByName);
            foreach ($clubByName as $club) 
            {
                $num = $club->getNumero();
                $numComplet = substr($num,0,-5);
                //dd($numComplet);
                if($numComplet == 128)
                {
                    $idAdversaire = $num;
                }
            }
            //dd($idAdversaire);
            $detailRencontreByLien = $this->api->getDetailsRencontreByLien($rencontre->getIsRetour(),$this->idClub,$idAdversaire);
            $partieRencontre = $detailRencontreByLien->getParties();
            //dd($partieRencontre);
            $match = new Matchs();
            foreach ($partieRencontre as $partie) 
            {
                if($partie->getAdversaireA() != 'Double')
                {
                    if($lettreTTL == 'B')
                    {
                        $nomJoueur = $partie->getAdversaireB();
                        //dump($nomJoueur);
                        $name = explode(" ",$nomJoueur);
                        $nameJoueur = $name[0];
                        $prenomJoueur = $name[1];
                        //dd($prenomJoueur);
                        $nomJoueurTTL = $joueursRepo->findOneByNomPrenom($nameJoueur,$prenomJoueur);
                        //dd($nomJoueurTTL);
                                //dd($match);
                                $match->setRencontre($rencontre);
                                $match->setJoueur($nomJoueurTTL);
                                $match->setScore($partie->getDetail());
                                if ($partie->getScoreB() == 1) 
                                {
                                    $match->setVictoire(true);
                                }
                                else
                                {
                                    $match->setVictoire(false);
                                }
                                    $entityManager = $this->getDoctrine()->getManager();
                                    $entityManager->persist($match);
                                    $entityManager->flush();   
                    }
                    if ($lettreTTL == 'A') 
                    {
                        $nomJoueur = $partie->getAdversaireA();
                        $name = explode(" ",$nomJoueur);
                        $nameJoueur = $name[0];
                        $prenomJoueur = $name[1];
                        //dd($prenomJoueur);
                        $nomJoueurTTL = $joueursRepo->findOneByNomPrenom($nameJoueur,$prenomJoueur);
                        //dd($nomJoueurTTL);
                                $match->setRencontre($rencontre);
                                $match->setJoueur($nomJoueurTTL);
                                $match->setScore($partie->getDetail());
                                if ($partie->getScoreB() == 1) 
                                {
                                    $match->setVictoire(true);
                                }
                                else
                                {
                                    $match->setVictoire(false);
                                }
                                $entityManager->persist($match);
                                $entityManager->flush();
                    }
                }
                
            }
            return $this->redirectToRoute('modifier_resultat_param',array('id' => $idRencontre));  
        }
          
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
            'matchs' => $matchs,
            'idRencontre' =>$id
        ]);
    }

    /**
     * @Route("/supprime/match/{id}", name="supprime_match")
     */
    public function supprimeEquipe(Request $request,MatchsRepository $matchRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $match = $matchRepo->find($id);
        
        if ($match) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($match);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('resultat_param');
        
    }
}
