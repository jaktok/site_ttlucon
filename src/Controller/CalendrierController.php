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

        return $this->render('calendrier/calendrier.html.twig', [
            'rencontres' => $rencontreRepo->findAll(),
            'equipes' => $equipeRepo->findAll(),
            'categorie' => $this->categorie,
        ]);
    }
}
