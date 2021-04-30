<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatController extends AbstractController
{
    /**
     * @Route("/resultat", name="resultat")
     */
    public function index(): Response
    {
        return $this->render('resultat/index.html.twig', [
            'controller_name' => 'ResultatController',
        ]);
    }
}
