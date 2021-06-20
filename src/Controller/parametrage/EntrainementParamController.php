<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EntrainementRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EntrainementType;
use App\Entity\Entrainement;
use App\Repository\InfosClubRepository;

class EntrainementParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/entrainement", name="entrainement_param")
     */
    public function index(Request $request,InfosClubRepository $infosClubRepo,EntrainementRepository $entraineRepo): Response
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
        $position = array_column($tabEntrainements, 'position');
        $heure = array_column($tabEntrainements, 'heure');
        array_multisort($position,$heure, SORT_ASC, $tabEntrainements);
        
        $form = $this->createFormBuilder($listeEntrainements)
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
        
        return $this->render('parametrage/entrainement_param/entrainement_param.html.twig', [
            'formEntrainements' => $form->createView(),
            'entrainements' => $tabEntrainements,
            'entraineurs' => $entraineurs,
            'dateVacances' => $dateVacances,
        ]);
    }
    
    /**
     * @Route("/dirigeant/param/entrainement/nouveau/", name="entrainement_param_nouveau")
     * @Route("/dirigeant/param/entrainement/modifier/{id}", name="entrainement_param_modif")
     *
     */
    public function gerer(Request $request, EntrainementRepository $entraineRepo, int $id = null): Response
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
        $position = array_column($tabEntrainements, 'position');
        $heure = array_column($tabEntrainements, 'heure');
        array_multisort($position,$heure, SORT_ASC, $tabEntrainements);
        
          if ($id){
            // recuperation de l enregistrements selectionne
              $entrainement = $entraineRepo->find($id);
              if ($entrainement) {
                $form = $this->createForm(EntrainementType::class,$entrainement);
                $form->handleRequest($request);
            }
        }
        else{
            $entrainement = new Entrainement();
            $form = $this->createForm(EntrainementType::class,($entrainement));
            $form->handleRequest($request);
        }
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entrainement);
            $entityManager->flush();
            return $this->redirectToRoute('entrainement_param');
        }
        return $this->render('parametrage/entrainement_param/fiche_entrainement.html.twig', [
            'formEntrainements' =>  $form->createView(),
            'idEntrainement' => $id,
            'entrainements' => $tabEntrainements
        ]);
        
    }
    
    /**
     * @Route("/dirigeant/param/entrainement/supprime/{id}", name="supprime_entrainement")
     */
    public function supprimeCompet(Request $request, EntrainementRepository $entraineRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $entrainement = $entraineRepo->find($id);
        if ($entrainement) {
            // On supprimer l entrainement
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($entrainement);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('entrainement_param');
        
    }
    
    
}
