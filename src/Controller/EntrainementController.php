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
            $tabEntrainements[$i]["entrainement"] = $entrainement;
            $tabEntrainements[$i]["heure"] = $entrainement->getHeureDebut();
            
            switch ($entrainement->getJour()) {
                case "Lundi":
                    $tabEntrainements[$i]["position"] = 1;
                    break;
                case "Mardi":
                    $tabEntrainements[$i]["position"] = 2;
                    break;
                case "Mercredi":
                    $tabEntrainements[$i]["position"] = 3;
                    break;
                case "Jeudi":
                    $tabEntrainements[$i]["position"] = 4;
                    break;
                case "Vendredi":
                    $tabEntrainements[$i]["position"] = 5;
                    break;
                case "Samedi":
                    $tabEntrainements[$i]["position"] = 6;
                    break;
                case "Dimanche":
                    $tabEntrainements[$i]["position"] = 7;
                    break;
                 default:
                     $tabEntrainements[$i]["position"] = 1;
            }
            $i++;
        }
       // dd($tabEntrainements);
        $position = array_column($tabEntrainements, 'position');
        $heure = array_column($tabEntrainements, 'heure');
        array_multisort($position,$heure, SORT_ASC, $tabEntrainements);
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
