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
            'equipes' => $equipeRepo->findBy(array(),array('nom' => 'ASC')),
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
    public function majAutoMatch(Request $request, RencontresRepository $rencontreRepo, MatchsRepository $matchRepo, JoueursRepository $joueursRepo, int $id = null): Response
    {
      //  $id = 333;
        $entityManager = $this->getDoctrine()->getManager();
        // recuperation de la rencontre selectionne
        $rencontre = $rencontreRepo->find($id);
        
        if ($rencontre) {
            if($rencontre->getDomicile()){
                $nomAversaire = $rencontre->getEquipeB();
                $nomTTL = $rencontre->getEquipeA();
                $lettreTTL = "A";
            }
            else{
                $nomAversaire = $rencontre->getEquipeA();
                $nomTTL = $rencontre->getEquipeB();
                $lettreTTL = "B";
            }
            $clubByName = $this->api->getClubsByName(trim(substr($nomAversaire,0,-2),'.'));
            // si on ne trouve pas on raccourci le nom pour des pb de formatage de nom fftt...
            if(empty($clubByName)){
                for ($i=3;$i<8;$i++){
                    if(empty($clubByName)){
                        $clubByName = $this->api->getClubsByName(trim(substr($nomAversaire,0,-$i),'.'));
                    }
                    else{
                        break;
                    }
                }
            }
            // si on trouve plusieurs clubs on garde le vendeen qui commence par 12
            /**@TODO
             *
             * Voir comment faire pour un cas regional
             **/
            if (sizeof($clubByName) > 1){
                $tabClubByName = array();
                for ($i=0;$i < sizeof($clubByName);$i++){
                    if (substr($clubByName[$i]->getNumero(), 0,2)==12){
                        $tabClubByName[0] = $clubByName[$i];
                    }
                }
                // on repasse le tableau avec le club vendeen remis dedans
                $clubByName = $tabClubByName;
            }
            
            $dateJour = new \DateTime();
            // la date du match doit etre inferieure a la date du jour sinon pas de resultat
            if (!empty($clubByName) && $rencontre->getDateRencontre()<=$dateJour){
                
                $idAversaire = $clubByName[0]->getNumero();
                // on teste si un resultat existe
                $isResultat = $this->api->isDetailsRencontreByLien($rencontre->getIsRetour(),$this->idClub,$idAversaire);
                
                if($isResultat){// si le resultat n existe pas on arrete
                    // on recupere le resultat fftt
                    $detailRencontreByLien = $this->api->getDetailsRencontreByLien($rencontre->getIsRetour(),$this->idClub,$idAversaire);
                    
                    // on parcours la tableau des resultats
                    foreach ($detailRencontreByLien->getParties() as $parties){
                        if ($parties->getAdversaireA()!="Double"){
                            $match = new Matchs();
                            $nomJoueur = "";
                            if($lettreTTL=="A"){
                                $nomJoueur = $parties->getAdversaireA();
                            }
                            else{
                                $nomJoueur = $parties->getAdversaireB();
                            }
                            $tabNom = explode(" ", $nomJoueur);
                            
                            $joueurByNom = $this->api->getJoueursByNom($tabNom[0],$tabNom[sizeof($tabNom)-1]);
                            $licence = $joueurByNom[0]->getLicence();
                            $joueurTTL = $joueursRepo->findOneByLicenceActif($licence);
                            $match->setJoueur($joueurTTL);
                            $match->setRencontre($rencontre);
                            if($lettreTTL=="A"){
                                $match->setVictoire($parties->getScoreA());
                            }
                            else{
                                $match->setVictoire($parties->getScoreB());
                            }
                            $match->setScore($parties->getDetail());
                            
                            // on verifie si le joueur est actif (joueurTTL non null
                            if ($joueurTTL != null){
                                // verif si deja enregistre on efface les enregistrements sans id joueur
                                $matchRepo->nettoieIdJoueurNull();
                                $existeMatch = $matchRepo->findByIdRencontreJoueurScore($rencontre->getId(),$joueurTTL->getId(),$parties->getDetail());
                                // on n enregistre que les nouveaux match
                                if($existeMatch == null){
                                    $entityManager->persist($match);
                                    $entityManager->flush();
                                }
                            }
                        }
                    }
                    // on enregistre le score rencontre
                    $rencontre->setScoreA($detailRencontreByLien->getScoreEquipeA());
                    $rencontre->setScoreB($detailRencontreByLien->getScoreEquipeB());
                    $entityManager->persist($rencontre);
                    $entityManager->flush();
                } // si pas de resultat
            }// si le nom club vide
            
        }
            return $this->redirectToRoute('modifier_resultat_param',array('id' => $id));  
          
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
