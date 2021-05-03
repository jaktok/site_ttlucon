<?php

namespace App\Controller;

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
        return $this->render('equipes_param/index.html.twig', [
            'controller_name' => 'EquipesParamController',
        ]);
    }
}
