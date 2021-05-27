<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EntrainementRepository;
use App\Entity\Entrainement;
use App\Repository\InfosClubRepository;

class EntrainementController extends AbstractController
{
    /**
     * @Route("/entrainement", name="entrainement")
     */
    public function index(Request $request , EntrainementRepository $entraineRepo, InfosClubRepository $infosClubRepo): Response
    {
        
        // recuperation de la liste des entrainements trié par jour heure debut 
        // heure fin en priant qu on en fasse pas le dimanche :)
        $listeEntrainements = $entraineRepo->findBy(array(),array('jour' => 'ASC','heure_debut' => 'ASC','heure_fin' => 'ASC'));
        
        $tabEntrainements = array();
        $i=0;
        $entrainement = new Entrainement();
        // recuperation du resultat dans un tableau
        foreach($listeEntrainements as $entrainement)
        {
            $tabEntrainements[$i] = $entrainement;
            $i++;
        }
        
        $form = $this->createFormBuilder($entrainement)
        ->getForm();
      
        $entraineurs = '';
        // recuperation de tous les enregistrements infoclub
        $listeInfosClub = $infosClubRepo->findAll();
        // recuperation du resultat dans un tableau infclub a passer a la vue
        $dateVacances = "";
        foreach($listeInfosClub as $infosClub)
        {
            if ($infosClub->getLibelle() == 'entraineurs'){
                $entraineurs = $infosClub->getContenu();
            }
            if($infosClub->getLibelle() =='date_arret_entrainement' ){
                $dateVacances  = $infosClub->getContenu();
            }
        }
        
        
        return $this->render('entrainement/entrainement.html.twig', [
            'entrainements' => $tabEntrainements,
            'entraineurs' => $entraineurs,
            'dateVacances' => $dateVacances,
        ]);
    }
}
