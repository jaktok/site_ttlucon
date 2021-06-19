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
        // recuperation de tout les entrainements
        $listeEntraine = $entraineRepo->findByOrder();
        
        $form = $this->createFormBuilder($listeEntraine)
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
            'entrainements' => $listeEntraine,
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
        
        // recuperation de tout les entrainements
        $listeEntraine = $entraineRepo->findBy(array(),array('jour' => 'ASC','heure_debut' => 'ASC','heure_fin' => 'ASC'));
        
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
            'entrainements' => $listeEntraine
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
