<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\Common\Persistence\ObjectManager;

use App\Repository\EquipeTypeRepository;

use App\Entity\EquipeType;

use App\Form\PrevisionEquipeType;

class EquipesParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/equipes", name="equipes_param")
     */
    public function equipesList(EquipeTypeRepository $equipes): Response
    {
        return $this->render('parametrage/equipes_param/equipes_param.html.twig', [
            'equipes' => $equipes->findAll(),
        ]);
    }

    /**
     * @Route("/dirigeant/param/equipes/new", name="equipes_param_new")
     * @Route("/dirigeant/param/equipes/modifier/{id}", name="equipes_param_modif")
     * 
     */
    public function createEquipe(Request $request, EquipeType $equipeTypes = null)
    {

        if(!$equipeTypes)
        {
            $equipeTypes = new EquipeType();
        }
       
        $form = $this->createForm(PrevisionEquipeType::class, $equipeTypes);
       
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipeTypes);
            $entityManager->flush();

            return $this->redirectToRoute('equipes_param');
        }
        //dd ($equipeTypes);
        return $this->render('parametrage/equipes_param/fiche_equipe_param.html.twig', [
            'formEquipe' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprime/equipe/{id}", name="supprime_equipe")
     */
    public function supprimeEquipe(Request $request, EquipeTypeRepository $equipeTypeRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $equipe = $equipeTypeRepo->find($id);
        
        if ($equipe) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipe);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('equipes_param');
        
    }
}
