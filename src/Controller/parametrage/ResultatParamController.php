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
use App\Entity\Joueurs;
use App\Form\JoueurMatchType;
use Doctrine\ORM\Query\AST\Functions\UpperFunction;

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
        $calendrierPhase1 = $rencontreRepo->findByPhase(1);

        $calendrierPhase2 = $rencontreRepo->findByPhase(2);
        return $this->render('parametrage/resultat_param/resultat_param.html.twig', [
            'resultats' => $rencontreRepo->findAll(),
            'calendrierPhase1' => $calendrierPhase1,
            'calendrierPhase2' => $calendrierPhase2,
            'equipes' => $equipeRepo->findBy(array(),array('nom' => 'ASC')),
        ]);
    }

    /**
     * @Route("/capitaine/param/resultat/modifier/{id}", name="modifier_resultat_param")
     */
    public function Resultat(Request $request,FichiersRepository $ficRepo,JoueursRepository $joueursRepo, MatchsRepository $matchRepo,RencontresRepository $rencontreRepo,Rencontres $rencontre): Response
    {
        if(!$rencontre)
        {
            $rencontre = new Rencontres();
        }

        $form = $this->createForm(ResultatsType::class, $rencontre);
        
        $form->handleRequest($request);

        $matchs = $matchRepo->findByIdRencontre($rencontre->getId());
        
        $matchsDouble  =array();
        $i = 0;
        // onparcours l etableau pour la gestion des doubles
        foreach ($matchs as $match){
            if ($match->getMatchDouble()){
               $player = $joueursRepo->find($match->getIdJoueur1());
                $match->setJoueur($player);
                $player2 = $joueursRepo->find($match->getIdJoueur2());
                $match->setScore($player2->getNom()." ".$player2->getPrenom());
                $matchsDouble[$i]["joueur1"] = $player;
                $matchsDouble[$i]["joueur2"] = $player2;
                $matchsDouble[$i]["victoire"] = $match->getVictoire();
                if ($match->getDouble1() =="1"){
                    $matchsDouble[$i]["numDouble"] = "1";
                }
                else{
                    $matchsDouble[$i]["numDouble"] = "2";
                }
                $matchsDouble[$i]["id"] = $match->getId();
                
                $i++;
                unset($matchs[array_search($match, $matchs)]);
            }
            $numDouble = array_column($matchsDouble, 'numDouble');
            array_multisort($numDouble, SORT_ASC, $matchsDouble);
        }
        //dd($matchsDouble);
        $idRencontre = $rencontre->getId();
        // Formatage des noms d'equipe pour le nom du fichier pdf
        $equipeA = $rencontre->getEquipeA();
        $equipeB = $rencontre->getEquipeB();
        if (strtoupper($equipeA) != "EXEMPT" ) {
            $nomA = explode(" ",$equipeA);
            $nomEquipeA = $nomA[0] . '-' . $nomA[1];
        }
        if (strtoupper($equipeB) != "EXEMPT") {
            $nomB = explode(" ",$equipeB);
            $nomEquipeB = $nomB[0] . '-' . $nomB[1];
        }
        

        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('fichier')->getData();
            
            $dt = new \DateTime();
            $nmImgDate =$dt->getTimestamp();
            
            if ($images){
                $fichier = $rencontre->getDateRencontre()->format('d-m-y').'-'.$nmImgDate.'.'.$images->guessExtension();
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

            return $this->redirectToRoute('modifier_resultat_param',array('id' => $rencontre->getId()));
        }
        $nomFicheRencontre = "";
        if ($rencontre->getFichier()){
            $nomFicheRencontre = $rencontre->getFichier()->getNom();
        }
        //dd($matchs);
        return $this->render('parametrage/resultat_param/fiche_resultat_param.html.twig', [
            'formResultat' => $form->createView(),
            'rencontre' => $rencontre,
            'matchs' => $matchs,
            'matchsDouble' => $matchsDouble,
            'idRencontre' => $idRencontre,
            'nomFicheResultat' => $nomFicheRencontre,
        ]);
    }


    /**
     * @Route("/capitaine/param/resultat/match/new/{idRencontre}", name="new_match_resultat_param")
     */
    public function doublemodifResultat(Request $request,JoueursRepository $joueursRepo, RencontresRepository $rencontreRepo,MatchsRepository $matchRepo, Matchs $match = null,int $idRencontre = null, int $id = null): Response
    {
       $match = new Matchs();
      
        $matchsDouble  =array();
        if($idRencontre){
            $matchs = $matchRepo->findByIdRencontre($idRencontre);
            $i = 0;
            $tabJoueurDoubleJoue = array();
            // onparcours l etableau pour la gestion des doubles
            foreach ($matchs as $matchDouble){
                if ($matchDouble->getMatchDouble()){
                    $player = $joueursRepo->find($matchDouble->getIdJoueur1());
                    $player2 = $joueursRepo->find($matchDouble->getIdJoueur2());
                    $matchsDouble[$i]["joueur1"] = $player;
                    $matchsDouble[$i]["joueur2"] = $player2;
                    $matchsDouble[$i]["victoire"] = $matchDouble->getVictoire();
                    if ($matchDouble->getDouble1() =="1"){
                        $matchsDouble[$i]["numDouble"] = "1";
                    }
                    else{
                        $matchsDouble[$i]["numDouble"] = "2";
                    }
                    $matchsDouble[$i]["id"] = $matchDouble->getId();
                    array_push($tabJoueurDoubleJoue,$player,$player2);
                    $i++;
                }
            }
            $numDouble = array_column($matchsDouble, 'numDouble');
            array_multisort($numDouble, SORT_ASC, $matchsDouble);
        }
        // recuperation de tous les joueurs actifs pour listes doubles
        $listeJoueurs = $joueursRepo->findByActif();
        $tabJoueurs= array();
        foreach ($listeJoueurs as $joueur){
            if (!in_array($joueur,$tabJoueurDoubleJoue))  {
                $tabJoueurs[$joueur->getNom()." ".$joueur->getPrenom()] = $joueur->getId();
            }
        }
        
        $form = $this->createForm(MatchsType::class, $match, array('tabJoueurs' => $tabJoueurs));
        $form->handleRequest($request);

        if ($idRencontre){
            $rencontre = $rencontreRepo->find($idRencontre);
        }
        if($form->isSubmitted() && $form->isValid()){
            $joueurUn = $joueursRepo->find($form->getData()->getIdJoueur1());
            $match->setJoueur($joueurUn);
            $match->setRencontre($rencontre);
            if ($form->getData()->getDouble1() == "1"){
                $match->setDouble1("1");
                $match->setDouble2("0");
            }
            else{
                $match->setDouble1("0");
                $match->setDouble2("1");
            }
            $match->setMatchDouble(true);
            $match->setIdJoueur1($form->getData()->getIdJoueur1());
            $match->setIdJoueur2($form->getData()->getIdJoueur2());
            $match->setVictoire($form->getData()->getVictoire());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('modifier_resultat_param',array('id' => $rencontre->getId()));  
        }
        return $this->render('parametrage/resultat_param/fiche_resultat_double_param.html.twig', [
            'formMatch' => $form->createView(),
            'idRencontre' => $idRencontre,
            'matchsDouble' => $matchsDouble,
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
        
        $joueurByClub = $this->api->getJoueursByClub($this->idClub);
        
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
                    $i = 0;
                    //dd($detailRencontreByLien);
                    // on parcours la tableau des resultats
                    foreach ($detailRencontreByLien->getParties() as $parties){
                        $i++;
                        if ($parties->getAdversaireA()!="Double"){
                            $match = new Matchs();
                            $nomJoueur = "";
                            $nomAdversaire = "";
                            $adversaireA = false;
                            $adversaireB = false;
                            if($lettreTTL=="A"){//dd($nomJoueur);
                                $nomJoueur = $parties->getAdversaireA();
                                $nomAdversaire =  $parties->getAdversaireB();
                                $adversaireA = true;
                            }
                            else{
                                $nomJoueur = $parties->getAdversaireB();
                                $nomAdversaire =  $parties->getAdversaireA();
                                $adversaireB = true;
                            }
                            $joueurTTLExiste = false;
                            foreach ($joueurByClub as $joueur){
                                if ($joueur->getNom()." ".$joueur->getPrenom()==$nomJoueur){
                                    $joueurTTLExiste = true;
                                }
                            }
                            if(!$joueurTTLExiste){//dd($joueurTTLExiste,$adversaireA);
                                if($adversaireA){
                                    $nomJoueur =   $parties->getAdversaireB();
                                    $nomAdversaire =  $parties->getAdversaireA();
                                }
                                else{
                                    $nomJoueur =   $parties->getAdversaireA();
                                    $nomAdversaire =  $parties->getAdversaireB();
                                }
                            }
                            $tabNom = explode(" ", $nomJoueur);//dd($tabNom);
                            
                           if (sizeof($tabNom) <= 3 ){
                               if (sizeof($tabNom) == 3){
                                   $nmJoueur = $tabNom[0]." ".$tabNom[1];
                               }
                               else{
                                   $nmJoueur = $tabNom[0];
                               }
                               $joueurByNom = $this->api->getJoueursByNom($nmJoueur,$tabNom[sizeof($tabNom)-1]);
                               if(sizeof($joueurByNom) > 1 ){
                                   $cp = 0;
                                   foreach ($joueurByNom as $jByNom){
                                       if ($jByNom->getClubId()!=$this->idClub){
                                           unset($joueurByNom[$cp]);
                                           $joueurByNom = array_values($joueurByNom);
                                       }
                                       $cp++;
                                   }
                               }
                               
                              // if($nomJoueur=="BLANCHARD Thierry"){ dd($nomJoueur,$joueurTTLExiste,$joueurByNom);  }
                                if (null == $joueurByNom){
                                    foreach ($joueurByClub as $joueur){
                                        if ($joueur->getNom()==$nmJoueur && $joueur->getPrenom()==$tabNom[sizeof($tabNom)-1]){
                                            $licence = $joueur->getLicence();
                                        }
                                    }
                                }
                                else{
                                    $licence = $joueurByNom[0]->getLicence();
                                }
                                $joueurTTL = $joueursRepo->findOneByLicenceActif($licence);
                                $match->setJoueur($joueurTTL);
                                $match->setRencontre($rencontre);
                                if($lettreTTL=="A" && $joueurTTLExiste){
                                    $match->setVictoire($parties->getScoreA());
                                }
                                else if($lettreTTL=="A" && !$joueurTTLExiste){
                                $match->setVictoire($parties->getScoreB());
                                }
                                else if($lettreTTL=="B" && $joueurTTLExiste){
                                    $match->setVictoire($parties->getScoreB());
                                }
                                else if($lettreTTL=="B" && !$joueurTTLExiste){
                                    $match->setVictoire($parties->getScoreA());
                                }
                                $match->setScore($parties->getDetail());
                                
                                // on verifie si le joueur est actif (joueurTTL non null
                                if ($joueurTTL != null){
                                    // verif si deja enregistre on efface les enregistrements sans id joueur
                                    $matchRepo->nettoieIdJoueurNull();
                                    $existeMatch = $matchRepo->findByIdRencontreJoueurScore($rencontre->getId(),$joueurTTL->getId(),$parties->getDetail());
                                    // on n enregistre que les nouveaux match
                                    $match->setDouble1($nomAdversaire);
                                    if($existeMatch == null){
                                        $entityManager->persist($match);
                                        $entityManager->flush();
                                    }
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

/*     /**
     * @Route("/capitaine/param/resultat/double/{id}", name="double_resultat_param")
     */
  /*  public function doubleResultat(Request $request,int $id = null,MatchsRepository $matchRepo): Response
    {
        $matchs = $matchRepo->findByIdRencontre($id);
        //dd($matchs);
        //return $this->redirectToRoute('resultat_param');

        return $this->render('parametrage/resultat_param/double_param.html.twig', [
            'matchs' => $matchs,
            'idRencontre' =>$id
        ]);
    } */

    /**
     * @Route("/supprime/match/{id}", name="supprime_match")
     */
    public function supprimeEquipe(Request $request,MatchsRepository $matchRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $match = $matchRepo->find($id);
        $idRencontre = $match->getRencontre()->getId();
        if ($match) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($match);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('modifier_resultat_param',array('id' => $idRencontre));
        
    }
    
    /**
     * @Route("/ajoute/joueursimple/{idRencontre}", name="ajoute_joueursimple")
     * @Route("/modifie/joueursimple/{idJoueur}", name="modifie_joueursimple")
     */
    public function ajouterJoueurMatch(Request $request,RencontresRepository $rencontreRepo, MatchsRepository $matchsRepo , JoueursRepository $joueursRepo, int $idRencontre = null,int $idJoueur = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $tabMatchs = array();
        
        if (!$idRencontre && $request->get('idRencontre')){
            $idRencontre = $request->get('idRencontre');
        }
        
        
        if ($idRencontre){
            // recuperation de l enregistrements selectionne
            $rencontre = $rencontreRepo->find($idRencontre);
        }
        $tableauMatchs = array();
        
        if ($idRencontre) {
            $j = 0;
            // rechercher les matchs li�s � l id competition
            $matchsCompet = $matchsRepo->findByIdRencontre($idRencontre);
            if ($matchsCompet){
                // recup nbjoueurs
                $tabJoueurs = array();
                $nbJoueur = 0;
                foreach ($matchsCompet as $match){
                    if (!in_array($match->getJoueur()->getId(),$tabJoueurs)){
                        $tabJoueurs[$nbJoueur]= $match->getJoueur()->getId();
                        $nbJoueur++;
                    }
                }
                foreach ($tabJoueurs as $idPlayer){
                    $nbVic = 0;
                    $nbDef = 0;
                    $joueur = $joueursRepo->find($idPlayer);
                    foreach ($matchsCompet as $match){
                        // recuperation de l enregistrements selectionne
                        if($match->getJoueur()->getId()==$idPlayer){
                            
                            if ($match->getVictoire()){
                                $nbVic++;
                            }
                            else{
                                $nbDef++;
                            }
                            $tableauMatchs[$j]["position"] =  $match->getPosition();
                        }
                    }
                    $tableauMatchs[$j]['nom'] = $joueur->getNom();
                    $tableauMatchs[$j]['prenom'] = $joueur->getPrenom();
                    $tableauMatchs[$j]["victoires"] =  trim($nbVic);
                    $tableauMatchs[$j]["defaites"] =  trim($nbDef);
                    $tableauMatchs[$j]["joueur"] =  $joueur;
                    $tableauMatchs[$j]['idJoueur'] = $joueur->getId();
                    $tableauMatchs[$j]['id'] = $idRencontre;
                    $j++;
                    
                }
            }
            
        }
        
        
        
        
        $renc = new Rencontres();
        if ($idRencontre){
            // recuperation de l enregistrements selectionne
            $renc = $rencontreRepo->find($idRencontre);
        }
        $joueur = new Joueurs();
        if ($idJoueur){
            $idRencontre = $request->query->get('idRencontre');
            $renc = $rencontreRepo->find($idRencontre);
            $joueur = $joueursRepo->find($idJoueur);
            
            // rechercher les matchs li�s � l id competition
            $matchsCompet = $matchsRepo->findByIdCompet($idRencontre);
            if ($matchsCompet){
                $nbVic = 0;
                $nbDef = 0;
                foreach ($matchsCompet as $match){
                    // recuperation de l enregistrements selectionne
                    if($match->getJoueur()->getId()==$idJoueur){
                        
                        if ($match->getVictoire()){
                            $nbVic++;
                        }
                        else{
                            $nbDef++;
                        }
                        $tabMatchs["position"] =  $match->getPosition();
                    }
                }
                $tabMatchs["victoires"] =  trim($nbVic);
                $tabMatchs["defaites"] =  trim($nbDef);
                $tabMatchs["joueur_compet"] =  $joueur;
            }
            
            $form = $this->createForm(JoueurMatchType::class,$tabMatchs);
            $form->handleRequest($request);
        }
        else {
            $form = $this->createForm(JoueurMatchType::class);
            $form->handleRequest($request);
        }
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $nbVictoires = (int) $data["victoires"];
            $nbDefaites = (int) $data["defaites"];
            
            // recup de la lidte des matchs pour suppression
            $player = new Joueurs();
            $player = $data["joueur_compet"];//dd($player,$data,$idJoueur);
            if($player){
                $idPlayer = $player->getId();
            }
            else{
                $idPlayer = $idJoueur;
                $player = $joueursRepo->find($idJoueur);
            }
            $listeMatchs = $matchsRepo->findByIdCompetJoueur($idRencontre,$idPlayer);
            foreach ($listeMatchs as $matche){
                $entityManager->remove($matche);
                $entityManager->flush();
            }
            
            for ($i = 0; $i < $nbVictoires;$i++ ){
                $match = new Matchs();
                $match->setVictoire(true);
                $match->setJoueur($player);
                $match->setRencontre($rencontre);
                $entityManager->persist($match);
                $entityManager->flush();
            }
            for ($i = 0; $i < $nbDefaites;$i++ ){
                $match = new Matchs();
                $match->setVictoire(false);
                $match->setJoueur($player);
                $match->setRencontre($rencontre);
                $match->setPosition($data["position"]);
                $entityManager->persist($match);
                $entityManager->flush();
            }
            
            return $this->redirectToRoute('modifier_resultat_param',array('id' => $rencontre->getId()));
        }
        //dd($idRencontre);
        return $this->render('parametrage/resultat_param/fiche_simple_param.html.twig', [
            'formJoueurMatch' => $form->createView(),
            'idRencontre'=> $idRencontre,
            'tabMatchs' => $tabMatchs,
            'tableauMatchs' => $tableauMatchs,
            'rencontre'  =>  $renc,
        ]);
        
    }
    
    /**
     * @Route("/dirigeant/supprime/fiche/{id}", name="supprime_fiche")
     */
    public function supprimeImage(Request $request,RencontresRepository $rencontreRepo, FichiersRepository $fichierRepo, int $id = null): Response{
        $img = new Fichiers();
        $entityManager = $this->getDoctrine()->getManager();
        $rencontre = $rencontreRepo->find($id);
        if ($rencontre->getFichier()){
            $img = $fichierRepo->find($rencontre->getFichier()->getId());
        }
        if ($img != null && $img->getId()!=null ){
            $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
            
            if ($image) {
                
                // on va chercher le chemin defini dans services yaml
                if ($rencontre->getFichier()!=null){
                    $nomImage = $this->getParameter('resultats_destination').'/'.$rencontre->getFichier()->getNom();
                    // on verifie si image existe
                    if (file_exists($nomImage)){
                        // si elle existe on la supprime physiquement du rep public
                        unlink($nomImage);
                    }
                }
                
                // On supprimer l image
                $rencontre->setFichier(null);
                $idRencontre = $rencontre->getId();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rencontre);
                $entityManager->flush();
                
                $rencontre = $rencontreRepo->find($idRencontre);
                $entityManager->clear();
                $imageSupp = $fichierRepo->find($image);
                if($imageSupp){
                    $entityManager->remove($imageSupp);
                    $entityManager->flush();
                }
            }
        }
        return $this->redirectToRoute('modifier_resultat_param',array('id' => $rencontre->getId()));
        
    }
    
    
    /**
     * @Route("/dirigeant/supprime/matchs/{id}", name="supprime_matchs")
     */
    public function supprimeMatchRencontre(Request $request,RencontresRepository $rencontreRepo,MatchsRepository $matchsRepo,int $id = null): Response{
        $entityManager = $this->getDoctrine()->getManager();

        if ($id) {
            // rechercher les matchs li�s � l id competition
            $matchsCompet = $matchsRepo->findByIdRencontre($id);
            if ($matchsCompet){
                 foreach ($matchsCompet as $match){
                    if (!$match->getMatchDouble()){
                        $entityManager->remove($match);
                        $entityManager->flush();
                    }
                }
        
            }
            return $this->redirectToRoute('modifier_resultat_param',array('id' => $id));
        }
                
        return $this->redirectToRoute('resultat_param');
        
    }
    
    
}
