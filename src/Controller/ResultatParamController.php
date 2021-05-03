<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatParamController extends AbstractController
{
    /**
     * @Route("/resultat/param", name="resultat_param")
     */
    public function index(): Response
    {
        return $this->render('resultat_param/index.html.twig', [
            'controller_name' => 'ResultatParamController',
        ]);
    }
}
