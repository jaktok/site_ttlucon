<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FonctionRepository;
use App\Entity\Fonction;
use App\Repository\JoueursRepository;
use App\Form\FonctionsType;
use App\Repository\RencontresRepository;
use App\Repository\MatchsRepository;
use App\Repository\EquipeTypeRepository;

class FonctionController extends AbstractController
{
    
    /**
     * @Route("dirigeant/param/fonctions", name="fonctions")
     */
    public function index(Request $request, EquipeTypeRepository $equipeRepo, FonctionRepository $fonctionsRepo,RencontresRepository $rencontreRepo, JoueursRepository $joueurRepo, MatchsRepository $matchRepo): Response
    {
        // recuperation de toutes les fonctions
        $listeFonction = $fonctionsRepo->findBy(array(), array('position' => 'ASC'));
        
        $form = $this->createFormBuilder($listeFonction)
        ->getForm();
        
        return $this->render('parametrage/fonction/fonction.html.twig', [
            'formFonctions' => $form->createView(),
            'fonctions' => $listeFonction
        ]); 
    }
    
    /**
     * @Route("/dirigeant/param/fonction/nouveau/", name="fonction_param_nouveau")
     * @Route("/dirigeant/param/fonction/modifier/{id}", name="fonction_param_modif")
     *
     */
    public function gerer(Request $request, FonctionRepository $fonctionRepo, JoueursRepository $joueursRepo, int $id = null): Response
    {
        
        // recuperation de toutes les fonctions
        $listeFonction = $fonctionRepo->findBy(array(), array('position' => 'ASC'));
        
        // recuperation de tous les joueurs tries sur le nom
        $listeJoueurs = $joueursRepo->findBy(array(),array('nom' => 'ASC'));
        //dd($id);
        if ($id){
            // recuperation de l enregistrements selectionne
            $fonction = $fonctionRepo->find($id);
            if ($fonction) {
                $form = $this->createForm(FonctionsType::class,$fonction);
                $form->handleRequest($request);
            }
        }
        else{
            $fonction = new Fonction();
            $form = $this->createForm(FonctionsType::class,($fonction));
            $form->handleRequest($request);
        }
       // dd($form);
        if($form->isSubmitted() && $form->isValid()){
            //dd($form);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fonction);
            $entityManager->flush();
          //  dd($form);
            return $this->redirectToRoute('fonctions');
        }
         // dd($fonction);
        return $this->render('parametrage/fonction/fiche_fonction.html.twig', [
            'formFonctions' =>  $form->createView(),
            'idFonction' => $id,
            'fonctions' => $listeFonction
        ]);
        
    }
    
    /**
     * @Route("/dirigeant/supprime/fonction/{id}", name="supprime_fonction")
     */
    public function supprimeCompet(Request $request, FonctionRepository $fonctionRepo, JoueursRepository $joueursRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $fonction = $fonctionRepo->find($id);
        if ($fonction) {
            // On supprimer la fonction
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fonction);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('fonctions');
        
    }
    
    
}
