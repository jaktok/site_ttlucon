<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipeTypeRepository;
use App\Repository\RencontresRepository;


class CalendrierController extends AbstractController
{
    private $categorie;

    /**
     * @Route("/calendrier/{cat}", name="calendrier")
     */
    public function index(Request $request, RencontresRepository $rencontreRepo,EquipeTypeRepository $equipeRepo,$cat=null): Response
    {
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
        }

        $calendrierPhase1 = $rencontreRepo->findByPhase(1);
        
        // test pour dev phase 2 sur la phase 1
        //$calendrierPhase2 = $rencontreRepo->findByPhase(2);
        $calendrierPhase2 = $rencontreRepo->findByPhase(1);
        
        return $this->render('calendrier/calendrier.html.twig', [
            'rencontres' => $rencontreRepo->findAll(),
            'calendrierPhase1' => $calendrierPhase1,
            'calendrierPhase2' => $calendrierPhase2,
            'equipes' => $equipeRepo->findAll(),
            'categorie' => $this->categorie,
        ]);
    }
}
