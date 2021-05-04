<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\InfosClub;
use App\Repository\InfosClubRepository;
use Symfony\Component\Validator\Constraints\Length;

class PresentationController extends AbstractController
{
    /**
     * @Route("/presentation", name="presentation")
     */
    public function index(InfosClubRepository $infosClubRepo): Response
    {
        $infosClub = new InfosClub();
        
        
        $listeInfosClub = $infosClubRepo->findAll();
  
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
