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
use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Repository\ClassementRepository;
use App\Entity\Classement;
use Symfony\Component\Validator\Constraints\IsNull;

class JoueursParamController extends AbstractController
{
    
    private $dernierClassement; 
    private $licencies; 
    
    /**
     * @Route("/dirigeant/param/joueurs", name="joueurs_param")
     */
    public function index(Request $request,ClassementRepository $classementRepo, RoleRepository $roleRepo, JoueursRepository $joueursRepo): Response
    {
        $joueurs = new Joueurs();
        
        
        $role = new Role();
        // recuperation de la liste des roles
        $listeRoles = $roleRepo->findAll();
        
        // recuperation de tous les joueurs
        $listeJoueurs = $joueursRepo->findAll();
        // recuperation du resultat dans un tableau licencies a passer a la vue
        foreach($listeJoueurs as $player)
        {
            $licencie = new Licencie();
            $joueur = new Joueurs();
            $joueur = $player;
            
            foreach ($listeRoles as $role){
                if ($joueur->getRole() == $role){
                    $joueur->setRole($role);
                }
                
            }
            // mappage de l entite joueur vers objet licencier
            $licencie = $this->mapperJoueurLicencie($joueur, $classementRepo, $roleRepo);
            
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
    public function gerer(Request $request,ClassementRepository $classementRepo, RoleRepository $roleRepo, JoueursRepository $joueursRepo, int $id = null): Response
    {
        
        $role = new Role();
        // recuperation de la liste des roles
        $listeRoles = $roleRepo->findAll();
        // mise a 0 du dernier classement pour eviter enregistrement si classement non modifie
        $this->dernierClassement = 0;
        $classement = new Classement();
        
        if ($id){
            
            $licencie = new Licencie();
            // recuperation de l enregistrements selectionne
            $joueur = $joueursRepo->find($id);
            
            foreach ($listeRoles as $role){
                if ($joueur->getRole() == $role){
                    $joueur->setRole($role);
                }
                
            }
            
            $licencie = $this->mapperJoueurLicencie($joueur, $classementRepo, $roleRepo);
;
            $form = $this->createForm(LicencieType::class,$licencie);
          // dd($form);
           $form->handleRequest($request);
        }
        else{
            $joueur = new Joueurs();
            $form = $this->createForm(LicencieType::class,($licencie = new Licencie()));
            $form->handleRequest($request);
        }
       
        if($form->isSubmitted() && $form->isValid()){
           // dd($licencie->getRole());
            $joueur->setAdresse($licencie->getAdresse());
            $joueur->setBureau($licencie->getBureau());
            $joueur->setCertificat($licencie->getCertificat());
            $joueur->setContactNom($licencie->getContactNom());
            $joueur->setContactPrenom($licencie->getContactPrenom());
            $joueur->setContactTel($licencie->getContactTel());
            $joueur->setCotisation($licencie->getCotisation());
            $joueur->setCp($licencie->getCp());
            $dateCertif = $licencie->getDateCertificat()->format('d/m/Y');
            $joueur->setDateCertificat($licencie->getDateCertificat());
            $dateNaissance = $licencie->getDateNaissance()->format('d/m/Y');
            $joueur->setDateNaissance($licencie->getDateNaissance());
            $joueur->setDivers($licencie->getDivers());
            $joueur->setIndiv($licencie->getIndiv());
            $joueur->setMail($licencie->getMail());
            $joueur->setNom($licencie->getNom());
            $joueur->setNomPhoto($licencie->getNomPhoto());
            $joueur->setNumLicence($licencie->getNumLicence());
            $joueur->setPrenom($licencie->getPrenom());
            
            foreach ($listeRoles as $role){
                if ($licencie->getRole() == $role->getId()){
                    $joueur->setRole($role);
                }
            }
            
            //$joueur->setRole($licencie->getRole());
            $joueur->setTelephone($licencie->getTelephone());
            $joueur->setVille($licencie->getVille());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joueur);
            $entityManager->flush();
            
            if ($this->dernierClassement == 0 || $this->dernierClassement != $licencie->getClassement() ){
                $classement->setJoueur($joueur);
                $classement->setPoints($licencie->getClassement());
                $classement->setDate(new \DateTime());
                $entityManager->persist($classement);
                $entityManager->flush();
            }
            
            
            return $this->redirectToRoute('joueurs_param');
        }

        
        return $this->render('parametrage/joueurs_param/fiche_joueur_param.html.twig', [
            'formJoueur' => $form->createView(),
            'joueur' => $licencie,
            'jouer' => $joueur
        ]);
    }
    
    public function mapperJoueurLicencie(Joueurs $joueur,ClassementRepository $classementRepo, RoleRepository $roleRepo):Licencie {
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
