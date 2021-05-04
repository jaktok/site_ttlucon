<?php

namespace App\Controller\parametrage;

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
        return $this->render('parametrage/infos_club_param/infos_club_param.html.twig', [
            'controller_name' => 'InfosClubParamController',
        ]);
    }
}
