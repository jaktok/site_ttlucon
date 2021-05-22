<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticlesRepository;
use App\Repository\FichiersRepository;
use App\Form\ArticlesType;
use App\Entity\Articles;
use App\Entity\Fichiers;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Component\Validator\Constraints\Collection;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/capitaine/param/articles", name="articles")
     */
    public function index(Request $request, ArticlesRepository $articleRepo): Response
    {
        
        // recuperation de tous les articles
        $listeArticles = $articleRepo->findBy(array(),array('date' => 'DESC'));
       // dd($listeArticle);
        $form = $this->createFormBuilder($listeArticles)
        ->getForm();
       //dd($listeArticles);
        return $this->render('parametrage/articles/articles.html.twig', [
            'formArticles' => $form->createView(),
            'articles' => $listeArticles
        ]);
    }
    
    /**
     * @Route("/capitaine/param/article/nouveau/", name="article_param_nouveau")
     * @Route("/capitaine/param/article/modifier/{id}", name="article_param_modif")
     *
     */
    public function gerer(Request $request, ArticlesRepository $articleRepo,FichiersRepository $ficRepo, int $id = null): Response
    {
        
                
        // recuperation de tout les articles
        $listeArticle = $articleRepo->findBy(array(),array('date' => 'ASC'));
        
        if ($id){
            // recuperation de l enregistrements selectionne
            $article = $articleRepo->find($id);
            
            $listFichier = $ficRepo->findByArticle($id);
            
            foreach ($listFichier as $fiche){
                $article->addFichier($fiche);
            }
            
            if ($article) {
                $form = $this->createForm(ArticlesType::class,$article);
                $form->handleRequest($request);
            }//dd($listFichier);
        }
        else{
            $article = new Articles();
            $form = $this->createForm(ArticlesType::class,($article));
            $form->handleRequest($request);
        }
        // dd($form);
        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('fichier')->getData();
            
            if ($images){
                $fichier = $article->getTitre().$article->getDate()->format('dmy').'.'.$images->guessExtension();
                // On copie le fichier dans le dossier uploads
                $images->move(
                    $this->getParameter('articles_destination'),
                    $fichier
                    );
            }
            $img = new Fichiers();
            $entityManager = $this->getDoctrine()->getManager();
            if ($article->getId()!=null){
                $img = $ficRepo->findOneByArticle($article->getId());
            }
            if ($images && $img!=null&&$img->getId()!=null) {
                $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
                $image->setNom($fichier);
                $image->setUrl($this->getParameter('articles_destination'));
                $entityManager->flush();
            }
            
            if (($img==null || $img->getId()==null) && isset($fichier)) {
                // On crée l'image dans la base de données
                $img = new Fichiers();
                $img->setNom($fichier);
                $img->setArticles($article);
                $img->setUrl($this->getParameter('articles_destination'));
                //dd($img);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($img);
                $entityManager->flush();
            }
            //$article->setAuteur($article->getJoueur()->getNom()." ".$article->getJoueur()->getPrenom());
          //  dd($images);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            //  dd($form);
            return $this->redirectToRoute('articles');
        }
        
     //   $nmPhoto = new Collection();
        
        $listFiches = array();
        $i=0;
        if($article->getFichier()!=null){
            foreach ($article->getFichier() as $fic){
                $nmPhoto = $fic->getNom();
                $listFiches[$i]= $nmPhoto;
                $i++;
            }
           // dd($nmPhoto);
        }
        // dd($fonction);
        //dd($article);
        return $this->render('parametrage/articles/fiche_article.html.twig', [
            'formArticles' =>  $form->createView(),
            'idArticle' => $id,
            'articles' => $listeArticle,
            'nomPhotos' => $listFiches,
        ]);
        
    }
    
    /**
     * @Route("/capitaine/article/{id}", name="supprime_article")
     */
    public function supprimeArticle(Request $request, ArticlesRepository $articleRepo, FichiersRepository $fichierRepo, int $id = null): Response
    {
        
        // recuperation de l enregistrements selectionne
        $article = $articleRepo->find($id);
        if ($article) {
            // On supprime les fichiers liés aux articles
            $fiche = $fichierRepo->findByArticle($id);
            if ($fiche){
                foreach ($fiche as $fichier){
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($fichier);
                    $entityManager->flush();
                }
            } 
           
            // On supprimer l article
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('articles');
        
    }
    
    /**
     * @Route("/supprime/image/{id}", name="supprime_image_article")
     */
    public function supprimeImage(Request $request,FichiersRepository $fichierRepo, int $id = null): Response{
        
        $img = new Fichiers();
        $entityManager = $this->getDoctrine()->getManager();
        $img = $fichierRepo->findOneByArticle($id);
        
        if ($img != null){
            $image = $entityManager->getRepository(Fichiers::class)->find($img->getId());
            
            if ($image) {
                // On supprimer l image
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($image);
                $entityManager->flush();
                // on va chercher le chemin defini dans services yaml
                $nomImage = $this->getParameter('articles_destination').'/'.$img->getNom();
                // on verifie si image existe
                if (file_exists($nomImage)){
                    // si elle existe on la supprime physiquement du rep public
                    unlink($nomImage);
                }
            }
        }
        return $this->redirectToRoute('article_param_modif',array('id' => $id));
        
    }
    
}
