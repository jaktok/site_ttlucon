<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionParamController extends AbstractController
{
    /**
     * @Route("/competition/param", name="competition_param")
     */
    public function index(): Response
    {
        return $this->render('competition_param/index.html.twig', [
            'controller_name' => 'CompetitionParamController',
        ]);
    }
}
