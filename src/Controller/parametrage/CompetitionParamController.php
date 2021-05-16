<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CompetitionRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Repository\TypeCompetitionRepository;

class CompetitionParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/competition", name="competition_param")
     */
    public function index(Request $request,CompetitionRepository $competRepo, TypeCompetitionRepository $typeCompetRepo): Response
    {
        
        // recuperation de la liste des competition
        $listeCompet = $competRepo->findAll();
        
        // recuperation de la liste des Types competition
        $listeTypeCompet = $typeCompetRepo->findAll();
        
        foreach ($listeCompet as $compet){
            $competition = new Competition();
            $competition = $compet;
            foreach ($listeTypeCompet as $typeCompet){
                if ($competition->getTypeCompetition() == $typeCompet){
                    $competition->setTypeCompetition($typeCompet);
                }
            }
        }
        
        $form = $this->createFormBuilder($listeCompet)
        ->getForm();
       // dd($listeCompet);
        return $this->render('parametrage/competition_param/competitions_param.html.twig', [
            'formCompets' =>  $form->createView(),
            'compets'   => $listeCompet
        ]);
    }
    
    /**
     * @Route("/dirigeant/param/competition/nouveau/", name="compet_param_nouveau")
     * @Route("/dirigeant/param/competition/modifier/{id}", name="compet_param_modif")
     *
     */
    public function gerer(Request $request, CompetitionRepository $competRepo, TypeCompetitionRepository $typeCompetRepo, int $id = null): Response
    {
        // recuperation de la liste des Types competition
        $listeTypeCompet = $typeCompetRepo->findAll();
        
        if ($id){
            // recuperation de l enregistrements selectionne
            $competition = $competRepo->find($id);
            if ($competition) {
                foreach ($listeTypeCompet as $typeCompet){
                    if ($competition->getTypeCompetition() == $typeCompet){
                        $competition->setTypeCompetition($typeCompet);
                    }
                }
                $form = $this->createForm(CompetitionType::class,$competition);
                $form->handleRequest($request);
            }
        }
        else{
            $competition = new Competition();
            $form = $this->createForm(CompetitionType::class,($competition));
            $form->handleRequest($request);
        }
        
        if($form->isSubmitted() && $form->isValid()){
           // dd($competition);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($competition);
            $entityManager->flush();
            return $this->redirectToRoute('competition_param');
        }
        
        return $this->render('parametrage/competition_param/fiche_compet_param.html.twig', [
            'formCompet' => $form->createView(),
        ]);
        
    }
    
    /**
     * @Route("/supprime/compet/{id}", name="supprime_compet")
     */
    public function supprimeCompet(Request $request, CompetitionRepository $competRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $compet = $competRepo->find($id);
        if ($compet) {
            // On supprimer la compet
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compet);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('competition_param');
        
    }
    
    /**
     * @Route("/renseigne/compet/{id}", name="renseigne_compet")
     */
    public function renseignerCompet(Request $request, CompetitionRepository $competRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $compet = $competRepo->find($id);
        if ($compet) {
        
        }
        
        return $this->redirectToRoute('competition_param');
        
    }
    
    
    
}
