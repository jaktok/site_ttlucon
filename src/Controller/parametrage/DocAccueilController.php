<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DocAccueilRepository;
use App\Repository\FichiersRepository;
use App\Entity\DocAccueil;
use App\Form\DocAccueilType;
use App\Entity\Fichiers;
use phpDocumentor\Reflection\Types\String_;

class DocAccueilController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/doc", name="doc")
     */
    public function index(Request $request, DocAccueilRepository $docAccueilRepo): Response
    {
        
        // recuperation de tous les parenaire
        $listeDocs = $docAccueilRepo->findBy(array(),array('position' => 'ASC'));
        $form = $this->createFormBuilder($listeDocs)
        ->getForm();
        return $this->render('parametrage/doc_accueil_param/docs.html.twig', [
            'formDocs' => $form->createView(),
            'docs' => $listeDocs
        ]);
    }
    
     /**
     * @Route("/dirigeant/param/doc/nouveau/", name="doc_param_nouveau")
     * @Route("/dirigeant/param/doc/modifier/{id}", name="doc_param_modif")
     *
     */
    public function gerer(Request $request, DocAccueilRepository $docAccueilRepo,FichiersRepository $ficRepo, int $id = null): Response
    {
        
        
        // recuperation de tout les docs accueil
        $listeDocs = $docAccueilRepo->findBy(array(),array('position' => 'ASC'));
        
        if ($id){
            // recuperation de l enregistrements selectionne
            $doc = $docAccueilRepo->find($id);
            
            $fichier = $ficRepo->find($doc->getFichier()->getId());
            $doc->setFichier($fichier);
            // dd($fichier);
            if ($doc) {
                $form = $this->createForm(DocAccueilType::class,$doc);
                $form->handleRequest($request);
            }
        }
        else{
            $doc = new DocAccueil();
            $form = $this->createForm(DocAccueilType::class,($doc));
            $form->handleRequest($request);
        }
        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('fichier')->getData();
            
             $dt = new \DateTime();
             $nmImgDate =$dt->getTimestamp();
             
            if ($images){
                // On copie le fichier dans le dossier uploads
                $fichier = 'docaccueil'.$nmImgDate.'.'.$images->guessExtension();
                // si on a modifie l image on supprime l ancienne
                if ($doc->getFichier()!=null){
                    if($doc->getFichier()->getNom() != 'docaccueil'.$nmImgDate.'.'.$images->guessExtension()){
                        $nomImage = $this->getParameter('docaccueil_destination').'/'.$doc->getFichier()->getNom();
                            if (file_exists($nomImage)){
                            // on va chercher le chemin defini dans services yaml
                            // si elle existe on la supprime physiquement du rep public
                            unlink($nomImage);
                        }
                    }
                }
               // dd($this->getParameter('docaccueil_destination'),$images,$form,'docaccueil'.$nmImgDate.'.'.$images->guessExtension());
                $images->move($this->getParameter('docaccueil_destination'),
                    $fichier
                    );
            }
            $img = new Fichiers();
            $entityManager = $this->getDoctrine()->getManager();
            if ($doc->getId()!=null){
                $img = $ficRepo->find($doc->getFichier()->getId());
            }
            if ($images && $img!=null&&$img->getId()!=null) {
                $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
                $image->setNom($fichier);
                $image->setUrl($this->getParameter('docaccueil_destination'));
                $entityManager->flush();
            }
            
            if (($img==null || $img->getId()==null) && isset($fichier)) {
                // On crée l'image dans la base de données
                $img = new Fichiers();//dd($fichier);
                $img->setNom($fichier);
                $img->setUrl($this->getParameter('docaccueil_destination'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($img);
                $entityManager->flush();
            }
            
            $doc->setFichier($img);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($doc);
            $entityManager->flush();
            //  dd($form);
            return $this->redirectToRoute('doc');
        }
        
        $nmPhoto = new String_();
        $idImage = '';
        if($doc->getFichier()!=null){
            $nmPhoto = $doc->getFichier()->getNom();
            $idImage = $doc->getFichier()->getId();
        }
        
       //dd($idImage,$nmPhoto);
        return $this->render('parametrage/doc_accueil_param/fiche_doc.html.twig', [
            'formDocs' =>  $form->createView(),
            'idDoc' => $id,
            'docs' => $listeDocs,
            'nomFichier' => $nmPhoto,
            'idImage' => $idImage,
        ]);
        
    }
    
    /**
     * @Route("/dirigeant/doc/{id}", name="supprime_doc")
     */
    public function supprimeDoc(Request $request, DocAccueilRepository $docAccueilRepo, FichiersRepository $fichierRepo, int $id = null): Response
    {
        
        // recuperation de l enregistrements selectionne
        $doc = $docAccueilRepo->find($id);
        if ($doc) {
            $idDoc = $doc->getFichier()->getId();
            $nmDoc = $doc->getFichier()->getNom();
            // On supprimer le doc
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($doc);
            $entityManager->flush();
            
            // On supprime le fichiers lié au doc
            $fichier = $docAccueilRepo->find($idDoc);
           
            if ($fichier){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($fichier);
                $entityManager->flush();
            }
            // on va chercher le chemin defini dans services yaml
            $nomImage = $this->getParameter('docaccueil_destination').'/'.$nmDoc;
            // on verifie si image existe
            if (file_exists($nomImage)){
                // si elle existe on la supprime physiquement du rep public
                unlink($nomImage);
            }

        }
        
        return $this->redirectToRoute('doc');
        
    }
    
}