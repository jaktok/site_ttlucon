<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JoueursParamController extends AbstractController
{
    /**
     * @Route("/joueurs/param", name="joueurs_param")
     */
    public function index(): Response
    {
        return $this->render('joueurs_param/index.html.twig', [
            'controller_name' => 'JoueursParamController',
        ]);
    }
}
