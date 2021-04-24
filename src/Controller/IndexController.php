<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        
        $api = new FFTTApi\FFTTApi("SW624", "93hUQWRcr6");
        $lienDivision = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6489&D1=3714&virtuel=0";
        $partie = $api->getRencontrePouleByLienDivision($lienDivision);
        $joueurs = "toto";
        dd($partie);
        
        /*return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);*/
    }
}
