<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrainementParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/entrainement", name="entrainement_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/entrainement_param/entrainement_param.html.twig', [
            'controller_name' => 'EntrainementParamController',
        ]);
    }
}
