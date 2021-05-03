<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfosClubParamController extends AbstractController
{
    /**
     * @Route("/infos/club/param", name="infos_club_param")
     */
    public function index(): Response
    {
        return $this->render('infos_club_param/index.html.twig', [
            'controller_name' => 'InfosClubParamController',
        ]);
    }
}
