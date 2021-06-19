<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FichiersRepository;
use App\Form\PartenaireType;
use App\Entity\Partenaire;
use App\Entity\Fichiers;
use phpDocumentor\Reflection\Types\String_;

class PartenaireParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/partenaire", name="partenaire_param")
     */
    public function index(Request $request, PartenaireRepository $partenaireRepo): Response
    {
        
        // recuperation de tous les parenaire
        $listePartenaires = $partenaireRepo->findBy(array(),array('nom' => 'DESC'));
        $form = $this->createFormBuilder($listePartenaires)
        ->getForm();
        return $this->render('parametrage/partenaire_param/partenaires.html.twig', [
            'formPartenaires' => $form->createView(),
            'partenaires' => $listePartenaires
        ]);
    }
    
    /**
     * @Route("/dirigeant/param/partenaire/nouveau/", name="partenaire_param_nouveau")
     * @Route("/dirigeant/param/partenaire/modifier/{id}", name="partenaire_param_modif")
     *
     */
    public function gerer(Request $request,  PartenaireRepository $partenaireRepo,FichiersRepository $ficRepo, int $id = null): Response
    {
        
        
        // recuperation de tout les partenairess
        $listePartenaires = $partenaireRepo->findBy(array(),array('nom' => 'ASC'));
        
        if ($id){
            // recuperation de l enregistrements selectionne
            $partenaire = $partenaireRepo->find($id);
            
            $fichier = $ficRepo->find($partenaire->getFichier()->getId());
            $partenaire->setFichier($fichier);
           // dd($fichier);
            if ($partenaire) {
                $form = $this->createForm(PartenaireType::class,$partenaire);
                $form->handleRequest($request);
            }
        }
        else{
            $partenaire = new Partenaire();
            $form = $this->createForm(PartenaireType::class,($partenaire));
            $form->handleRequest($request);
        }
        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('fichier')->getData();
   
            $dt = new \DateTime();
            $nmImgDate =$dt->getTimestamp();
            
        if ($images){
                // On copie le fichier dans le dossier uploads
            $fichier = 'partenaire'.$nmImgDate.'.'.$images->guessExtension();
            // si on a modifie l image on supprime l ancienne
            if ($partenaire->getFichier()!=null){
                if($partenaire->getFichier()->getNom() != 'partenaire'.$nmImgDate.'.'.$images->guessExtension()){
                    $nomImage = $this->getParameter('partenaires_destination').'/'.$partenaire->getFichier()->getNom();
                    if (file_exists($nomImage)){
                        // on va chercher le chemin defini dans services yaml
                        // si elle existe on la supprime physiquement du rep public
                        unlink($nomImage);
                    }
                }
            }
            $images->move($this->getParameter('partenaires_destination'),
                    $fichier
                    );
        }
            $img = new Fichiers();
            $entityManager = $this->getDoctrine()->getManager();
            if ($partenaire->getId()!=null){
                $img = $ficRepo->find($partenaire->getFichier()->getId());
            }
            if ($images && $img!=null&&$img->getId()!=null) {
                $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
                $image->setNom($fichier);
                $image->setUrl($this->getParameter('partenaires_destination'));
                $entityManager->flush();
            }
            
            if (($img==null || $img->getId()==null) && isset($fichier)) {
                // On crée l'image dans la base de données
                $img = new Fichiers();//dd($fichier);
                $img->setNom($fichier);
                $img->setUrl($this->getParameter('partenaires_destination'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($img);
                $entityManager->flush();
            }

            $partenaire->setFichier($img);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();
            //  dd($form);
            return $this->redirectToRoute('partenaire_param');
        }
        
        $nmPhoto = new String_();
        $idImage = '';
        if($partenaire->getFichier()!=null){
            $nmPhoto = $partenaire->getFichier()->getNom();
            $idImage = $partenaire->getFichier()->getId();
        }
        
        
        return $this->render('parametrage/partenaire_param/fiche_partenaire.html.twig', [
            'formPartenaires' =>  $form->createView(),
            'idPartenaire' => $id,
            'partenaires' => $listePartenaires,
            'nomFichier' => $nmPhoto,
            'idImage' => $idImage,
        ]);
        
    }
    
    /**
     * @Route("/dirigeant/partenaire/{id}", name="supprime_partenaire")
     */
    public function supprimePartenaire(Request $request, PartenaireRepository $partenaireRepo, FichiersRepository $fichierRepo, int $id = null): Response
    {
        
        // recuperation de l enregistrements selectionne
        $partenaire = $partenaireRepo->find($id);
        if ($partenaire) {
            $idPart = $partenaire->getFichier()->getId();
            $nmDoc = $partenaire->getFichier()->getNom();
            // On supprimer le partenaire
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($partenaire);
            $entityManager->flush();
            
            // On supprime le fichiers lié au partenaire
            $fichier = $fichierRepo->find($idPart);
           // dd($fichier,$partenaire->getFichier()->getId());
            if ($fichier){
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($fichier);
                    $entityManager->flush();
            }
            // on va chercher le chemin defini dans services yaml
            $nomImage = $this->getParameter('partenaires_destination').'/'.$nmDoc;
            // on verifie si image existe
            if (file_exists($nomImage)){
                // si elle existe on la supprime physiquement du rep public
                unlink($nomImage);
            }
            

        }
        
        return $this->redirectToRoute('partenaire_param');
        
    }
    
}
