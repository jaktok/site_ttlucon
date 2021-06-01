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
        $listeEquipes = $equipeRepo->findBy(array(), array(
            'nom' => 'ASC'
        ));

        $listeJoueurs = $joueurRepo->findAll();

        $tabBrule = array();
        
/*         foreach ($listeEquipes as $equipe) {
            $idEquipe = $equipe->getId();
            $numEquipe = $equipe->getNumEquipe();
            $listeRencontres = $rencontreRepo->findByEquipe($idEquipe);
            $listeJoueursMatch = $matchRepo->findIdJoueursMatch($listeRencontres);
            $i=0;
            foreach ($listeJoueurs as $joueur) {
               
                foreach ($listeJoueursMatch as $joueurMatch) {
                    if ($joueur && $joueurMatch->getJoueur()) {
                        if ($joueur->getId() == $joueurMatch->getJoueur()->getId()) {
                            if (empty($tabBrule[$numEquipe][$i]["nbMatchs"])) {
                                $tabBrule[$numEquipe][$i]["nbMatchs"] = 1;
                                $tabBrule[$numEquipe][$i]["joueur"] = $joueur;
                                $tabBrule[$numEquipe][$i]["equipe"] = [$equipe];
                                
                                $idRencontre = $joueurMatch->getRencontre()->getId();
                            } else {
                                if ($idRencontre == null || $idRencontre != $joueurMatch->getRencontre()->getId()) {
                                    //dd($tabBrule,$i);
                                    $tabBrule[$numEquipe][$i]["nbMatchs"] += 1;
                                    $idRencontre = $joueurMatch->getRencontre()->getId();
                                }
                            }
                            // dd($joueurMatch);
                        }
                    }
                   
                }
                $i++;
            }
        } */

       // dd($tabBrule);
        $listeJoueursMatchEquipe = $matchRepo->findIdJoueursMatchEquipe();
        //dd($listeJoueursMatchEquipe);
        $numJoueur = 0;
        foreach ($listeJoueurs as $joueur) {
            foreach ($listeEquipes as $equipe) {
                $idEquipe = $equipe->getId();
                $listeRencontres = $rencontreRepo->findByEquipe($idEquipe);
              // dd($listeRencontres);
                $tabRencontres = array();
                $cpt = 0;
                foreach ($listeRencontres as $renc){
                    $tabRencontres[$cpt] = $renc["id"];
                    $cpt++;
                }
                $numEquipe = $equipe->getNumEquipe();
                foreach ($listeJoueursMatchEquipe as $joueurMatch) {
                   // dd($joueurMatch);
                    if ($joueur && $joueurMatch->getJoueur()) {
                        if ($joueur->getId() == $joueurMatch->getJoueur()->getId() && in_array($joueurMatch->getRencontre()->getId(),$tabRencontres)) {
                            if (empty($tabBrule[$numJoueur][$numEquipe]["nbMatchs"])) {
                                $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] = 1;
                                $tabBrule[$numJoueur]['joueur'] = $joueur;
                                $tabBrule[$numJoueur][$numEquipe]["joueur"] = $joueur;
                                $tabBrule[$numJoueur][$numEquipe]["equipe"] = [$equipe];
                                
                                $idRencontre = $joueurMatch->getRencontre()->getId();
                            } else {
                                if ($idRencontre == null || $idRencontre != $joueurMatch->getRencontre()->getId()) {
                                    //dd($tabBrule,$i);
                                    $tabBrule[$numJoueur][$numEquipe]["nbMatchs"] += 1;
                                    $idRencontre = $joueurMatch->getRencontre()->getId();
                                }
                            }
                            // dd($joueurMatch);
                        }
                    }
                    
                }
            }
            $numJoueur++;
        }
       // dd($tabBrule);
        

        return $this->render('brule/brule.html.twig', [
            'tabBrule' => $tabBrule,
            'tabEquipes' => $listeEquipes,
        ]);
    }
}
