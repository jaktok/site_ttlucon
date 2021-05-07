<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendrierParamController extends AbstractController
{
    /**
     * @Route("/param/calendrier", name="calendrier_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/calendrier_param/calendrier_param.html.twig', [
            'controller_name' => 'CalendrierParamController',
        ]);
    }
}
