<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EntrainementRepository;

class EntrainementController extends AbstractController
{
    /**
     * @Route("/entrainement", name="entrainement")
     */
    public function index(Request $request , EntrainementRepository $entraineRepo): Response
    {
        
        // recuperation de la liste des entrainements
        $listeEntrainements = $entraineRepo->findBy(array(),array('jour' => 'ASC'));
        
        $tabEntrainements = array();
        $i=0;
        // recuperation du resultat dans un tableau
        foreach($listeEntrainements as $entrainement)
        {
            $tabEntrainements[$i] = $entrainement;
            $i++;
        }
        
        $form = $this->createFormBuilder($entrainement)
        ->getForm();
      
        //dd($this->fonctions);
        
        return $this->render('entrainement/entrainement.html.twig', [
            'entrainements' => $tabEntrainements,
        ]);
    }
}
