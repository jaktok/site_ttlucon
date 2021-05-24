<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PartenaireRepository;
use App\Repository\FichiersRepository;

class PartenaireController extends AbstractController
{
    /**
     * @Route("/partenaire", name="partenaire")
     */
    public function index(Request $request, PartenaireRepository $partenaireRepo, FichiersRepository $ficRepo): Response
    {
        // recuperation de tous les partenaire
        $listePartenaires = $partenaireRepo->findBy(array(),array('nom' => 'DESC'));
        $tabPartenaires = array();
        $i = 0;
        foreach ($listePartenaires as $partenaire){
            $tabPartenaires[$i]['titre'] = $partenaire->getNom();
            $tabPartenaires[$i]['texte'] = $partenaire->getTexte();
            
            // recup id image
            $idImg = $partenaire->getFichier()->getId();
            $image = $ficRepo->find($idImg);
            $tabPartenaires[$i]['nomPhoto'] = $image->getNom(); 
            $i++;
            
        }
        
        return $this->render('partenaire/partenaire.html.twig', [
            'partenaires' => $tabPartenaires,
        ]);
    }
}
