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
        $tabPhase1 = array("01","02","03","04","05","06");
        if (in_array($moisEncours, $tabPhase1)){
            $phase = 1;
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
                $numEquipe = $equipe->getNumEquipe();//dd($listeJoueursMatchEquipe);
                foreach ($listeJoueursMatchEquipe as $joueurMatch) {
                    if ($joueur && $joueurMatch->getJoueur()) {
                        // on regarde si le joueur qu on parcours = le joueur match et si il est dans l equipe qu on parcours (dans tab idrencontres)
                        if ($joueur->getId() == $joueurMatch->getJoueur()->getId() && in_array($joueurMatch->getRencontre()->getId(),$tabRencontres)) {
                            // si oui et que c est le premier passage on renseigne le tabbrule
                            if($joueur->getId()=="53"){array_push($tabId,$joueurMatch->getRencontre()->getId());}
                            if (empty($tabBrule[$numJoueur][$numEquipe]["nbMatchs"])) {
                                $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] = 1;
                                $tabBrule[$numJoueur]['joueur'] = $joueur;
                                $tabBrule[$numJoueur][$numEquipe]["joueur"] = $joueur;
                                $tabBrule[$numJoueur][$numEquipe]["equipe"] = [$equipe];
                                $tabBrule[$numJoueur][$numEquipe]["idRencontre"] = $joueurMatch->getRencontre()->getId();
                                $idRencontre = $joueurMatch->getRencontre()->getId();
                                
                            } else {
                                    // si il est dans le tab rencontre 
                                if ($idRencontre == null || $idRencontre != $joueurMatch->getRencontre()->getId() && $tabBrule[$numJoueur][$numEquipe]["idRencontre"]!= $joueurMatch->getRencontre()->getId()  ) {
                                    $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] += 1;
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
            if ($tabBrule[$i][1]["nbMatchs"]+$tabBrule[$i][2]["nbMatchs"]+$tabBrule[$i][3]["nbMatchs"]+$tabBrule[$i][4]["nbMatchs"]>0){
                $tabBruleTrie[$cpt]=$tabBrule[$i];
                $cpt++;
            }
        }
       // dd($tabBruleTrie);
        return $this->render('brule/brule.html.twig', [
            'tabBrule' => $tabBruleTrie,
            'tabEquipes' => $listeEquipes,
        ]);
    }
}
