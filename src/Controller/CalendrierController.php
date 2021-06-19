<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipeTypeRepository;
use App\Repository\RencontresRepository;
use App\Repository\CompetitionRepository;
use App\Entity\Competition;
use App\Repository\CategoriesRepository;


class CalendrierController extends AbstractController
{
    private $categorie;

    /**
     * @Route("/calendrier/{cat}", name="calendrier")
     */
    public function index(Request $request,CategoriesRepository $categoriesRepo, CompetitionRepository $competRepo, RencontresRepository $rencontreRepo,EquipeTypeRepository $equipeRepo,$cat=null): Response
    {
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
                break;
            case "Adapte":
                $this->categorie = "Adapte";
                break;
            default :
                $this->categorie = "Adulte";
        }

        // recuperation de la liste des competition
        $listeCompet = $competRepo->findAll();
        
        // recuperation de la liste des Types competition
        $listeCategories = $categoriesRepo->findAll();
        
        foreach ($listeCompet as $compet){
            $competition = new Competition();
            $competition = $compet;
            foreach ($listeCategories as $categorie){
                if ($competition->getCategories() == $categorie){
                    $competition->setCategories($categorie);
                }
            }
        }
        
        
        if( $this->categorie != "Adapte"){
        
            $calendrierPhase1 = $rencontreRepo->findByPhase(1);
            
            // test pour dev phase 2 sur la phase 1
            $calendrierPhase2 = $rencontreRepo->findByPhase(2);
            return $this->render('calendrier/calendrier.html.twig', [
                'rencontres' => $rencontreRepo->findAll(),
                'calendrierPhase1' => $calendrierPhase1,
                'calendrierPhase2' => $calendrierPhase2,
                'equipes' => $equipeRepo->findAll(),
                'categorie' => $this->categorie,
                'compets'   => $listeCompet,
            ]);
        }
        else{
        // recuperation de la liste des competition
        $listeCompet = $competRepo->findAdapte();
        return $this->render('calendrier/calendrier.html.twig', [
            'compets'   => $listeCompet,
            'categorie' => $this->categorie,
            'compets'   => $listeCompet,
        ]);
        }

    }
}
