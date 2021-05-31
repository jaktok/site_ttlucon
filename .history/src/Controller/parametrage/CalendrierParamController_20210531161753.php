<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Rencontres;
use App\Entity\EquipeType;
use App\Form\RencontreType;

use App\Repository\RencontresRepository;

class CalendrierParamController extends AbstractController
{
    

    /**
     * @Route("/dirigeant/param/calendrier/new/{idTeam}", name="calendrier_param_new")
     * @Route("/dirigeant/param/calendrier/modifier/{id}", name="calendrier_param_modif")
     * 
     */ 
    public function createCalendrier(Request $request,RencontresRepository $rencontreRepo, int $id = null, int $idTeam = null)
    {
        $rencontre = new Rencontres();
        $idEquipe= $idTeam ;
        if($id)
        {
            $rencontre = $rencontreRepo->find($id);
            $idEquipe = $rencontre->getEquipeType()->getId();
            $dateRencontre = $rencontre->getDateRencontre();
            //dd($rencontre->getNoJournee());
        }
        $dateRencontre = null;

        $form = $this->createForm(RencontreType::class, $rencontre);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $idEquipe = $form["equipeType"]->getData()->getId();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rencontre);
            $entityManager->flush();

            return $this->redirectToRoute('calendrier_param',array('id' => $idEquipe));
        }
       // dd($idEquipe);
        return $this->render('parametrage/calendrier_param/fiche_calendrier_param.html.twig', [
            'formRencontre' => $form->createView(),
            'idEquipe' => $idEquipe,
            'dateRencontre' => $dateRencontre,
        ]);
    }

    /**
     * @Route("/supprime/calendrier/{id}", name="supprime_calendrier")
     */
    public function supprimeCalendrier(Request $request, RencontresRepository  $rencontreRepo, int $id = null, int $idTeam=null): Response
    {
        $idEquipe= $idTeam ;
        if($id)
        {
            $rencontre = $rencontreRepo->find($id);
            $idEquipe = $rencontre->getEquipeType()->getId();
        }

        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $rencontre = $rencontreRepo->find($id);
        
        if ($rencontre) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rencontre);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('calendrier_param',array('id' => $idEquipe));
        
    }

    /**
     * @Route("/dirigeant/param/calendrier/{id}", name="calendrier_param")
     */ 
    public function index(RencontresRepository $rencontres, int $id = null): Response
    {

        //dd($rencontres->findByEquipe($id));
        //dd($id);
        return $this->render('parametrage/calendrier_param/calendrier_param.html.twig', [
            'rencontres' => $rencontres->findByEquipe($id),
            'idTeam' => $id,
        ]);
    }
}
