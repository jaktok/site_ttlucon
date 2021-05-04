<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipesParamController extends AbstractController
{
    /**
     * @Route("/equipes/param", name="equipes_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/equipes_param/equipes_param.html.twig', [
            'controller_name' => 'EquipesParamController',
        ]);
    }
}
