<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InfosClubRepository;
use App\Entity\InfosClub;
use App\Form\InfosClubType;

class InfosClubParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/infos/club/", name="infos_club_param")
     */
    public function index(Request $request, InfosClubRepository $infosClubRepo): Response
    {
        
        $infosClub = new InfosClub();

        // recuperation de tous les enregistrements infoclub
        $listeInfosClub = $infosClubRepo->findAll();
        // recuperation du resultat dans un tableau infclub a passer a la vue
        foreach($listeInfosClub as $infosClub)
        {
            $infClub[$infosClub->getId()][$infosClub->getLibelle()] = $infosClub->getContenu();
        }
 
        $form = $this->createFormBuilder($infosClub)
        ->getForm();
        
        return $this->render('parametrage/infos_club_param/infos_club_param.html.twig', [
            'formInfos' => $form->createView(),
            'infosclub' => $listeInfosClub
        ]);  
    }
    
    /**
     * @Route("/dirigeant/param/infos/club/gerer/{id}", name="infos_club_param_gerer")
     * 
     */
    public function gerer(Request $request, InfosClubRepository $infosClubRepo,int $id): Response
    {
        
        // recuperation de l enregistrements selectionne
        $infoClub = $infosClubRepo->find($id);
        
        $form = $this->createForm(InfosClubType::class,$infoClub);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($infoClub);
            $entityManager->flush();
            
            return $this->redirectToRoute('infos_club_param');
        }
        
        return $this->render('parametrage/infos_club_param/infos_club_param.html.twig', [
            'formInfos' => $form->createView(),
            'infoclub' => $infoClub,
        ]);
    }
}
