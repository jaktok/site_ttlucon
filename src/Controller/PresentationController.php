<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\InfosClub;
use App\Repository\InfosClubRepository;

class PresentationController extends AbstractController
{
    /**
     * @Route("/presentation", name="presentation")
     */
    public function index(InfosClubRepository $infosClubRepo): Response
    {
        $infClub = array();
        $infosClub = new InfosClub();
        // recuperation de tous les enregistrements infoclub
        $listeInfosClub = $infosClubRepo->findAll();
        // recuperation du resultat dans un tableau infclub a passer a la vue
       foreach($listeInfosClub as $infosClub)
       {
           $infClub[$infosClub->getLibelle()] = $infosClub->getContenu();
       }
       
       return $this->render('presentation/presentation.html.twig', [
            'controller_name' => 'PresentationController',
           'infosclub' => $infClub
        ]);
    }
}
