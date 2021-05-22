<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FonctionRepository;

class BureauController extends AbstractController
{
    
    private $fonctions;
    
    public function __construct()
    {
        // Recuperation infos config.ini
        $this->fonctions = array(); 
    }
    
    /**
     * @Route("/bureau", name="bureau")
     */
    public function index(Request $request,FonctionRepository $fonctionsRepo): Response
    {
        
        // recuperation de la liste des categories
        $listeFonctions = $fonctionsRepo->findBy(array(),array('position' => 'ASC'));
        
        $i=0;
        // recuperation du resultat dans un tableau 
        foreach($listeFonctions as $fonction)
        {
            $this->fonctions[$i]=$fonction;
            $i++;
        }
        
        $form = $this->createFormBuilder($this->fonctions)
        ->getForm();
        
        return $this->render('bureau/bureau.html.twig', [
            'fonctions' => $this->fonctions,
        ]);
    }
}
