<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JoueursParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/joueurs", name="joueurs_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/joueurs_param/joueurs_param.html.twig', [
            'controller_name' => 'JoueursParamController',
        ]);
    }
}
