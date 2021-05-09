<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JoueursRepository;
use App\Entity\Joueurs;
use App\Form\LicencieType;
use App\Model\Licencie;
use App\Repository\ClassementRepository;
use App\Entity\Classement;
use Symfony\Component\Validator\Constraints\IsNull;
use App\Repository\CategoriesRepository;
use App\Entity\Categories;

class JoueursParamController extends AbstractController
{
    
    private $dernierClassement; 
    private $licencies; 
    
    /**
     * @Route("/joueurs/param", name="joueurs_param")
     */
    public function index(Request $request,ClassementRepository $classementRepo, CategoriesRepository $categoriesRepo, JoueursRepository $joueursRepo): Response
    {
        $joueurs = new Joueurs();
        
        
        $categorie = new Categories();
        // recuperation de la liste des categories
        $listeCategories = $categoriesRepo->findAll();
        
        // recuperation de tous les joueurs
        $listeJoueurs = $joueursRepo->findAll();
        // recuperation du resultat dans un tableau licencies a passer a la vue
        foreach($listeJoueurs as $player)
        {
            $licencie = new Licencie();
            $joueur = new Joueurs();
            $joueur = $player;
            foreach ($listeCategories as $categ){
                if ($joueur->getCategories() == $categ){
                    $joueur->setCategories($categ);
                }
                
            }
            // mappage de l entite joueur vers objet licencier
            $licencie = $this->mapperJoueurLicencie($joueur, $classementRepo);
            
            $this->licencies[]=$licencie;
        }
        
        $form = $this->createFormBuilder($this->licencies)
        ->getForm();
        
        //dd( $this->licencies);
        return $this->render('parametrage/joueurs_param/joueurs_param.html.twig', [
            'formJoueurs' => $form->createView(),
            'joueurs' => $this->licencies,
        ]);
    }
    
    /**
     * @Route("/joueur/param/nouveau/", name="joueur_param_nouveau")
     * @Route("/joueur/param/modifier/{id}", name="joueur_param_modif")
     *
     */
    public function gerer(Request $request,ClassementRepository $classementRepo,  CategoriesRepository $categoriesRepo, JoueursRepository $joueursRepo, int $id = null): Response
    {
        
        // recuperation de la liste des categories
        $listeCategories = $categoriesRepo->findAll();
        // mise a 0 du dernier classement pour eviter enregistrement si classement non modifie
        $this->dernierClassement = 0;
        $classement = new Classement();
        
        if ($id){
            
            $this->licencie = new Licencie();
            // recuperation de l enregistrements selectionne
            $joueur = $joueursRepo->find($id);
            
            foreach ($listeCategories as $categ){
                if ($joueur->getCategories() == $categ){
                    if ($joueur->getCategories() == $categ){
                        $joueur->setCategories($categ);
                        $this->licencie->setCategories($categ);
                        $libCategorie = $categ->getLibelle();
                    }
                }
            }
            $this->licencie = $this->mapperJoueurLicencie($joueur, $classementRepo);
            $form = $this->createForm(LicencieType::class,$this->licencie);
          // dd($form);
           $form->handleRequest($request);
        }
        else{
            $joueur = new Joueurs();
            $form = $this->createForm(LicencieType::class,($licencie = new Licencie()));
            $form->handleRequest($request);
        }
       
        if($form->isSubmitted() && $form->isValid()){
            $joueur->setAdresse($this->licencie->getAdresse());
            $joueur->setBureau($this->licencie->getBureau());
            $joueur->setCertificat($this->licencie->getCertificat());
            $joueur->setContactNom($this->licencie->getContactNom());
            $joueur->setContactPrenom($this->licencie->getContactPrenom());
            $joueur->setContactTel($this->licencie->getContactTel());
            $joueur->setCotisation($this->licencie->getCotisation());
            $joueur->setCp($this->licencie->getCp());
            $joueur->setDateCertificat($this->licencie->getDateCertificat());
            $joueur->setDateNaissance($this->licencie->getDateNaissance());
            $joueur->setDivers($this->licencie->getDivers());
            $joueur->setIndiv($this->licencie->getIndiv());
            $joueur->setMail($this->licencie->getMail());
            $joueur->setNom($this->licencie->getNom());
            $joueur->setNomPhoto($this->licencie->getNomPhoto());
            $joueur->setNumLicence($this->licencie->getNumLicence());
            $joueur->setPrenom($this->licencie->getPrenom());
            
            foreach ($listeCategories as $categ){
                if ($this->licencie->getCategories() == $categ){
                    $joueur->setCategories($categ);
                }
            }
            
            $joueur->setTelephone($this->licencie->getTelephone());
            $joueur->setVille($this->licencie->getVille());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joueur);
            $entityManager->flush();
            
            if ($this->dernierClassement == 0 || $this->dernierClassement != $this->licencie->getClassement() ){
                $classement->setJoueur($joueur);
                $classement->setPoints($this->licencie->getClassement());
                $classement->setDate(new \DateTime());
                $entityManager->persist($classement);
                $entityManager->flush();
            }
            
            
            return $this->redirectToRoute('joueurs_param');
        }

        
        return $this->render('parametrage/joueurs_param/fiche_joueur_param.html.twig', [
            'formJoueur' => $form->createView(),
            'joueur' => $this->licencie,
            'jouer' => $joueur,
            'libCategorie' => $libCategorie,
            'categorie' => $this->licencie->getCategories()
        ]);
    }
    
    public function mapperJoueurLicencie(Joueurs $joueur,ClassementRepository $classementRepo):Licencie {
       $licencier = new Licencie();
       
       // recuperation de la liste des classements pour le joueur
       $listeClassements = $classementRepo->findByIdJoueur($joueur->getId());
       If ($listeClassements){
           $licencier->setClassement($listeClassements[0]->getPoints());
           $this->dernierClassement = $listeClassements[0]->getPoints();
       }
       else{
           $licencier->setClassement("500");
       }
       $licencier->setId($joueur->getId());
       $licencier->setAdresse($joueur->getAdresse());
       $licencier->setBureau($joueur->getBureau());
       $licencier->setCertificat($joueur->getCertificat());
       $licencier->setContactNom($joueur->getContactNom());
       $licencier->setContactPrenom($joueur->getContactPrenom());
       $licencier->setContactTel($joueur->getContactTel());
       $licencier->setCotisation($joueur->getCotisation());
       $licencier->setCp($joueur->getCp());
       $licencier->setDateCertificat($joueur->getDateCertificat());
       $licencier->setDateNaissance($joueur->getDateNaissance());
       $licencier->setDivers($joueur->getDivers());
       $licencier->setIndiv($joueur->getIndiv());
       $licencier->setMail($joueur->getMail());
       $licencier->setNom($joueur->getNom());
       $licencier->setNomPhoto($joueur->getNomPhoto());
       $licencier->setNumLicence($joueur->getNumLicence());
       $licencier->setPrenom($joueur->getPrenom());
       $licencier->setTelephone($joueur->getTelephone());
       $licencier->setVille($joueur->getVille());
       
       return $licencier;
    }

    
}
