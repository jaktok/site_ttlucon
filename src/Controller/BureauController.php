<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FonctionRepository;
use App\Repository\JoueursRepository;

class BureauController extends AbstractController
{
    
    /**
     * @Route("/bureau", name="bureau")
     */
    public function index(Request $request , JoueursRepository $joueursRepo, FonctionRepository $fonctionsRepo): Response
    {
        
        // recuperation de la liste des categories
        $listeFonctions = $fonctionsRepo->findBy(array(),array('position' => 'ASC'));
         
        $fonctions = array(); 
        // recuperation du resultat dans un tableau 
        foreach($listeFonctions as $fonction)
        {
            array_push($fonctions,$fonction);
        }

        return $this->render('bureau/bureau.html.twig', [
            'fonctions' => $fonctions,
        ]);
    }
}
