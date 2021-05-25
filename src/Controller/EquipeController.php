<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipeTypeRepository;

use App\Entity\EquipeType;

class EquipeController extends AbstractController
{
    private $categorie;

    /**
     * @Route("/equipe/{cat}", name="equipe")
     */
    public function index(Request $request, EquipeTypeRepository $equipes,$cat=null): Response
    {
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
        }

        return $this->render('equipe/equipe.html.twig', [
            'equipes' => $equipes->findAll(),
            'categorie' => $this->categorie,
        ]);
    }
}
