<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipeTypeRepository;
use App\Repository\RencontresRepository;
use App\Repository\JoueursRepository;
use App\Repository\MatchsRepository;

class BruleController extends AbstractController
{

    /**
     *
     * @Route("/brule", name="brule")
     */
    public function index(Request $request, EquipeTypeRepository $equipeRepo, RencontresRepository $rencontreRepo, JoueursRepository $joueurRepo, MatchsRepository $matchRepo): Response
    {

        // rencontres lucon
        $listeEquipes = $equipeRepo->findByAdultes();
        
        
        $listeJoueurs = $joueurRepo->findByActif();

        $tabBrule = array();
        
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
        $tabId = array();
        $listeJoueursMatchEquipe = $matchRepo->findIdJoueursMatchEquipeSimple();
        $numJoueur = 0;
        //On parcours tout les joueurs
        foreach ($listeJoueurs as $joueur) {
            // On parcours toutes les equipes
            foreach ($listeEquipes as $equipe) {
                $idEquipe = $equipe->getId();
                $listeRencontres = $rencontreRepo->findByIdEquipeAndPhaseAdultes($idEquipe,$phase);
                $tabRencontres = array();
                $cpt = 0;
                // On recupere les id rencontre de l equipe en cours
                foreach ($listeRencontres as $renc){
                    $tabRencontres[$cpt] = $renc["id"];
                    $cpt++;
                }//if($joueur->getId()=="53"){dd($tabRencontres);}
                $numEquipe = $equipe->getNumEquipe();
                foreach ($listeJoueursMatchEquipe as $joueurMatch) {
                    if ($joueur && $joueurMatch->getJoueur()) {
                        // on regarde si le joueur qu on parcours = le joueur match et si il est dans l equipe qu on parcours (dans tab idrencontres)
                        if ($joueur->getId() == $joueurMatch->getJoueur()->getId() && in_array($joueurMatch->getRencontre()->getId(),$tabRencontres)) {
                            // si oui et que c est le premier passage on renseigne le tabbrule
                            if (empty($tabBrule[$numJoueur][$numEquipe]["nbMatchs"])) {
                                $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] = 1;
                                $tabBrule[$numJoueur]["totMatchs"] = 1;
                                $tabBrule[$numJoueur]['joueur'] = $joueur;
                                $tabBrule[$numJoueur][$numEquipe]["joueur"] = $joueur;
                                $tabBrule[$numJoueur][$numEquipe]["equipe"] = [$equipe];
                                // on rajoute  id rencontre pour ne pas rajouter les autres matchs
                                $tabBrule[$numJoueur][$numEquipe]["idRencontre"] = $joueurMatch->getRencontre()->getId();
                                $idRencontre = $joueurMatch->getRencontre()->getId();
                                
                            } else {
                                    // si il est dans le tab rencontre  et on verifie qu'on a pas deja renseigne id rencontre plus haut
                                if ($idRencontre == null || $idRencontre != $joueurMatch->getRencontre()->getId() && $tabBrule[$numJoueur][$numEquipe]["idRencontre"]!= $joueurMatch->getRencontre()->getId()  ) {
                                    $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] += 1;
                                    $tabBrule[$numJoueur]["totMatchs"] += 1;
                                    $idRencontre = $joueurMatch->getRencontre()->getId();
                                }
                            }
                        }
                    }
                    
                }
                if(!isset($tabBrule[$numJoueur][$numEquipe]["nbMatchs"])){
                    $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] = 0;
                    $tabBrule[$numJoueur]['joueur'] = $joueur;
                    $tabBrule[$numJoueur][$numEquipe]["joueur"] = $joueur;
                    $tabBrule[$numJoueur][$numEquipe]["equipe"] = [$equipe];
                }
            }// fin boucle equipe
            $numJoueur++;
        }// fin boucle joueur
  
        // suppression des joueurs n ayant fait aucun match
        $tabBruleTrie = array();
        $cpt=0;
       
        for ($i = 0; $i < sizeof($tabBrule); $i++) {
            if($tabBrule[$i]!=null ){
                if(isset($tabBrule[$i]["totMatchs"]) && $tabBrule[$i]["totMatchs"] > 0){
                    $tabBruleTrie[$cpt]=$tabBrule[$i];
                    $cpt++;
                }
            }
        }
        return $this->render('brule/brule.html.twig', [
            'tabBrule' => $tabBruleTrie,
            'tabEquipes' => $listeEquipes,
        ]);
    }
}
