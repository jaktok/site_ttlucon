<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatParamController extends AbstractController
{
    /**
     * @Route("/capitaine/param/resultat", name="resultat_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/resultat_param/resultat_param.html.twig', [
            'controller_name' => 'ResultatParamController',
        ]);
    }
}
