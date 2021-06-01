<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Integer;
use App\Repository\EquipeTypeRepository;
use App\Repository\FichiersRepository;
use App\Repository\RencontresRepository;
use App\Entity\EquipeType;
use App\Entity\Fichiers;
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
    public function createEquipe(Request $request,FichiersRepository $fichierRepo,EquipeTypeRepository $equipeRepo, EquipeType $equipeTypes = null)
    {

        if(!$equipeTypes)
        {
            $equipeTypes = new EquipeType();
        }
        
        $form = $this->createForm(PrevisionEquipeType::class, $equipeTypes);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $name = $form->get('nom')->getData();
            $nameTeam = $equipeRepo->findOneByNom($name);

            $numero = explode(" ",$name);
            $numeroFinal = $numero[1];
            $equipeTypes->setNumEquipe($numeroFinal);

            $numTeam = $equipeRepo->findOneByNum($numeroFinal);
            if(!$equipeTypes->getId()){
                if($nameTeam){
                    return $this->render('parametrage/equipes_param/error_name_equipe_param.html.twig', [
                        'equipes' => $equipeTypes
                    ]);
                    }
                    if($numTeam){
                        return $this->render('parametrage/equipes_param/error_num_equipe_param.html.twig', [
                            'equipes' => $equipeTypes
                        ]);
                        }
            }
            

            


            $images = $form->get('nom_photo')->getData();
            //dd($equipeTypes->getNom());
            if ($images){
                $fichier = $equipeTypes->getNom().'.'.$images->guessExtension();
                // On copie le fichier dans le dossier uploads
                $images->move(
                         $this->getParameter('equipes_destination'),
                         $fichier
                         );
            }

            $img = new Fichiers();
            $entityManager = $this->getDoctrine()->getManager();
            if ($equipeTypes->getId()!=null){
                $img = $fichierRepo->findOneByEquipeType($equipeTypes->getId());
            }

            if ($images && $img!=null&&$img->getId()!=null) {
                $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
                $image->setNom($fichier);
                $image->setUrl($this->getParameter('equipes_destination'));
                $entityManager->flush();
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipeTypes);
            $entityManager->flush();

            if (($img==null || $img->getId()==null) && isset($fichier)) {
                // On cr�e l'image dans la base de donn�es
                $img = new Fichiers();
                $img->setNom($fichier);
                $img->setEquipeType($equipeTypes);
                $img->setUrl($this->getParameter('equipes_destination'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($img);
                $entityManager->flush();
            }

            return $this->redirectToRoute('equipes_param');
        }

        $nmPhoto = new String_();
        if($equipeTypes->getPhoto()!=null){
            $nmPhoto = $equipeTypes->getPhoto()->getNom();
        }


        return $this->render('parametrage/equipes_param/fiche_equipe_param.html.twig', [
            'formEquipe' => $form->createView(),
            'nomPhoto' => $nmPhoto,
            'equipe' => $equipeTypes
        ]);
    }

    /**
     * @Route("/supprime/equipe/{id}", name="supprime_equipe")
     */
    public function supprimeEquipe(Request $request,RencontresRepository $rencontreRepo,EquipeTypeRepository $equipeTypeRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $equipe = $equipeTypeRepo->find($id);
        
        if ($equipe) {

            $listeRencontre = $rencontreRepo->findByEquipe($equipe->getId());
            if ($listeRencontre){
                foreach ($listeRencontre as $rencontre)
                    $entityManager->remove($rencontre);
                    $entityManager->flush();
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipe);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('equipes_param');
        
    }

    /**
     * @Route("/dirigeant/supprime/equipe/image/{id}", name="supprime_equipe_img")
     */
    public function supprimeImage(Request $request,FichiersRepository $fichierRepo, int $id = null): Response{
        $img = new Fichiers();
        $entityManager = $this->getDoctrine()->getManager();
        $img = $fichierRepo->findOneByEquipeType($id);
        //dd($img);
        if ($img != null){
            $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
            //dd($image);
            if ($image) {
                // On supprimer l image
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($image);
                $entityManager->flush();
                // on va chercher le chemin defini dans services yaml
                $nomImage = $this->getParameter('equipes_destination').'/'.$img->getNom();
                // on verifie si image existe
                if (file_exists($nomImage)){
                    // si elle existe on la supprime physiquement du rep public
                    unlink($nomImage);
                }
            }
        }
        return $this->redirectToRoute('equipes_param');
        
    }
}
