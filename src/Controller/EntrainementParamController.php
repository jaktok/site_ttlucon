<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrainementParamController extends AbstractController
{
    /**
     * @Route("/entrainement/param", name="entrainement_param")
     */
    public function index(): Response
    {
        return $this->render('entrainement_param/index.html.twig', [
            'controller_name' => 'EntrainementParamController',
        ]);
    }
}
