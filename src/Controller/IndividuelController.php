<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TypeCompetitionRepository;
use App\Repository\CompetitionRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Competition;
use App\Repository\MatchsRepository;
use App\Repository\JoueursRepository;

class IndividuelController extends AbstractController
{
    
    private $categorie;
    
    /**
     * @Route("/individuel/{cat}", name="individuel")
     */
    public function index(Request $request, JoueursRepository $joueursRepo, MatchsRepository $matchsRepo ,CompetitionRepository $competRepo, TypeCompetitionRepository $typeCompetRepo, $cat=null): Response
    {
        
        // recuperation de la liste des competition
        $listeCompet = $competRepo->findAll();
        
        // recuperation de la liste des Types competition
        $listeTypeCompet = $typeCompetRepo->findAll();
    
        $tabMatchs = array();
        $j = 0;
        $i = 0;
        foreach ($listeCompet as $compet){
            $competition = new Competition();
            $competition = $compet;
            foreach ($listeTypeCompet as $typeCompet){
                if ($competition->getTypeCompetition() == $typeCompet){
                    $competition->setTypeCompetition($typeCompet);
                }
            }
            
           // dd($compet,$listeCompet[$i]->getDate());
            
            // rechercher les matchs liés à l id competition
            $matchsCompet = $matchsRepo->findByIdCompet($compet->getId());
            $defaultData;
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
                foreach ($tabJoueurs as $idJoueur){
                    $nbVic = 0;
                    $nbDef = 0;
                    $joueur = $joueursRepo->find($idJoueur);
                    foreach ($matchsCompet as $match){
                        // recuperation de l enregistrements selectionne
                        if($match->getJoueur()->getId()==$idJoueur){
                            
                            if ($match->getVictoire()){
                                $nbVic++;
                            }
                            else{
                                $nbDef++;
                            }
                            $tabMatchs[$j]["position"] =  $match->getPosition();
                        }
                    }
                    $tabMatchs[$j]['nom'] = $joueur->getNom();
                    $tabMatchs[$j]['prenom'] = $joueur->getPrenom();
                    $tabMatchs[$j]["victoires"] =  trim($nbVic);
                    $tabMatchs[$j]["defaites"] =  trim($nbDef);
                    $tabMatchs[$j]["joueur"] =  $joueur;
                    $tabMatchs[$j]['idJoueur'] = $joueur->getId();
                    $tabMatchs[$j]['id'] = $compet->getId();
                    $j++;
                    
                }
            }
        }
        
        $form = $this->createFormBuilder($listeCompet)
        ->getForm();
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
                break;
            case "Adapte":
                $this->categorie = "Adapte";
        }
        $dtjour = new \DateTime();
        
       // dd($listeCompet,$tabMatchs);
        return $this->render('individuel/individuel.html.twig', [
            'formCompets' =>  $form->createView(),
            'compets'   => $listeCompet,
            'tabMatchs' => $tabMatchs,
            'categorie' => $this->categorie,
            'date' => $dtjour,
        ]);
    }
}
