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
     * @Route("/joueurs/param", name="joueurs_param")
     */
    public function index(Request $request,ClassementRepository $classementRepo, RoleRepository $roleRepo, JoueursRepository $joueursRepo): Response
    {
        $joueurs = new Joueurs();
        
        
        $role = new Role();
        // recuperation de la liste des roles
        $listeRoles = $roleRepo->findAll();
        
        // recuperation de tous les enregistrements infoclub
        $listeJoueurs = $joueursRepo->findAll();
        // recuperation du resultat dans un tableau infclub a passer a la vue
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
            
            // recuperation de la liste des classements pour le joueur
            $listeClassements = $classementRepo->findByIdJoueur($player->getId());
            If ($listeClassements){
                $licencie->setClassement($listeClassements[0]->getPoints());
                $this->dernierClassement = $listeClassements[0]->getPoints();
            }
            else{
                $licencie->setClassement("500");
            }
            $licencie->setId($joueur->getId());
            $licencie->setAdresse($joueur->getAdresse());
            $licencie->setBureau($joueur->getBureau());
            $licencie->setCertificat($joueur->getCertificat());
            $licencie->setContactNom($joueur->getContactNom());
            $licencie->setContactPrenom($joueur->getContactPrenom());
            $licencie->setContactTel($joueur->getContactTel());
            $licencie->setCotisation($joueur->getCotisation());
            $licencie->setCp($joueur->getCp());
            $dateCertif = $joueur->getDateCertificat()->format('d/m/Y');
            $licencie->setDateCertificat($joueur->getDateCertificat());
            $dateNaissance = $joueur->getDateNaissance()->format('d/m/Y');
            $licencie->setDateNaissance($joueur->getDateNaissance());
            $licencie->setDivers($joueur->getDivers());
            $licencie->setIndiv($joueur->getIndiv());
            $licencie->setMail($joueur->getMail());
            $licencie->setNom($joueur->getNom());
            $licencie->setNomPhoto($joueur->getNomPhoto());
            $licencie->setNumLicence($joueur->getNumLicence());
            $licencie->setPrenom($joueur->getPrenom());
            $licencie->setTelephone($joueur->getTelephone());
            $licencie->setVille($joueur->getVille());
            
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

        $this->dernierClassement = 0;
        
        $classement = new Classement();
        
        
        
        //dd($colRoles);
        
        if ($id){
            
            $licencie = new Licencie();
            // recuperation de l enregistrements selectionne
            $joueur = $joueursRepo->find($id);
            
            
            foreach ($listeRoles as $role){
                if ($joueur->getRole() == $role){
                    $joueur->setRole($role);
                }
                
            }
            
            // recuperation de la liste des classements pour le joueur
            $listeClassements = $classementRepo->findByIdJoueur($id);
            If ($listeClassements){
                $licencie->setClassement($listeClassements[0]->getPoints());
                $this->dernierClassement = $listeClassements[0]->getPoints();
            }
            else{
                $licencie->setClassement("500");
            }
            $licencie->setAdresse($joueur->getAdresse());
            $licencie->setBureau($joueur->getBureau());
            $licencie->setCertificat($joueur->getCertificat());
            $licencie->setContactNom($joueur->getContactNom());
            $licencie->setContactPrenom($joueur->getContactPrenom());
            $licencie->setContactTel($joueur->getContactTel());
            $licencie->setCotisation($joueur->getCotisation());
            $licencie->setCp($joueur->getCp());
            $dateCertif = $joueur->getDateCertificat()->format('d/m/Y');
            $licencie->setDateCertificat($joueur->getDateCertificat());
            $dateNaissance = $joueur->getDateNaissance()->format('d/m/Y');
            $licencie->setDateNaissance($joueur->getDateNaissance());
            $licencie->setDivers($joueur->getDivers());
            $licencie->setIndiv($joueur->getIndiv());
            $licencie->setMail($joueur->getMail());
            $licencie->setNom($joueur->getNom());
            $licencie->setNomPhoto($joueur->getNomPhoto());
            $licencie->setNumLicence($joueur->getNumLicence());
            $licencie->setPrenom($joueur->getPrenom());
            $licencie->setTelephone($joueur->getTelephone());
            $licencie->setVille($joueur->getVille());
           
            //dd($licencie);
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

    
}
