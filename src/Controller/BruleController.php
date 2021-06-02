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

        $listeJoueurs = $joueurRepo->findByActif();

        $tabBrule = array();
        
        $listeJoueursMatchEquipe = $matchRepo->findIdJoueursMatchEquipe();
        $numJoueur = 0;
        foreach ($listeJoueurs as $joueur) {
            foreach ($listeEquipes as $equipe) {
                $idEquipe = $equipe->getId();
                $listeRencontres = $rencontreRepo->findByIdEquipe($idEquipe);
              // dd($listeRencontres);
                $tabRencontres = array();
                $cpt = 0;
                foreach ($listeRencontres as $renc){
                    $tabRencontres[$cpt] = $renc["id"];
                    $cpt++;
                }
                $numEquipe = $equipe->getNumEquipe();
                foreach ($listeJoueursMatchEquipe as $joueurMatch) {
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
        

        return $this->render('brule/brule.html.twig', [
            'tabBrule' => $tabBrule,
            'tabEquipes' => $listeEquipes,
        ]);
    }
}