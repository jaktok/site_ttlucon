<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/competition", name="competition_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/competition_param/competition_param.html.twig', [
            'controller_name' => 'CompetitionParamController',
        ]);
    }
}
