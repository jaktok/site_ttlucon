<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi;

class JoueurController extends AbstractController
{
    
    private $ini_array;
    private $api;
    
    public function __construct()
    {
        // Recuperation infos config.ini
        $this->ini_array = parse_ini_file("../config/config.ini");
        $this->api = new FFTTApi\FFTTApi();
    }
    
    /**
     * @Route("/joueur", name="joueur")
     */
    public function index(): Response
    {
        
        $joueurByClub = $this->api->getJoueursByClub($this->ini_array['id_club_lucon']);
        
        return $this->render('joueur/joueur.html.twig', [
            'controller_name' => 'JoueurController',
            'joueurs'   => $joueurByClub
        ]);
    }
}
