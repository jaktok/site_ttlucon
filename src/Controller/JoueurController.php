<?php

namespace App\Controller;

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
use App\Repository\CategoriesRepository;
use FFTTApi\FFTTApi;

class JoueurController extends AbstractController
{
    
    private $ini_array;
    private $api;
    private $categorie;
    
    public function __construct()
    {
        // Recuperation infos config.ini
        $this->ini_array = parse_ini_file("../config/config.ini");
        $this->api = new FFTTApi();
    }
    
    /**
     * @Route("/joueur{cat}", name="joueur")
     */
    public function index(Request $request,ClassementRepository $classementRepo, CategoriesRepository $categoriesRepo, JoueursRepository $joueursRepo, $cat=null): Response
    {
        
        $joueurByClub = $this->api->getJoueursByClub($this->ini_array['id_club_lucon']);
            
            // recuperation de la liste des categories
            $listeCategories = $categoriesRepo->findAll();
            
            // recuperation de tous les joueurs
            $listeJoueurs = $joueursRepo->findBy(array(),array('nom' => 'ASC'));
            // recuperation du resultat dans un tableau licencies a passer a la vue
            foreach($listeJoueurs as $player)
            {
                $licencie = new Licencie();
                $joueur = new Joueurs();
                $joueur = $player;
                $libCat;
                foreach ($listeCategories as $categ){
                    if ($joueur->getCategories() == $categ){
                        $joueur->setCategories($categ);
                        $libCat = $categ->getLibelle();
                    }
                    
                }
                // mappage de l entite joueur vers objet licencier
                $licencie = $this->mapperJoueurLicencie($joueur, $classementRepo);
                $licencie->setLibelleCat($libCat);
                $this->licencies[]=$licencie;
            }
            
            $form = $this->createFormBuilder($this->licencies)
            ->getForm();
            
            switch ($cat) {
                case "tous":
                    $this->categorie = "tous";
                    break;
                case "Adulte":
                    $this->categorie = "Adulte";
                    break;
                case "Jeune":
                    $this->categorie = "Jeune";
                    break;
                case "Adapte":
                    $this->categorie = "Adapte";
            }
            
            
            return $this->render('joueur/joueur.html.twig', [
                'formJoueurs' => $form->createView(),
                'joueurs' => $this->licencies,
                'categorie' => $this->categorie,
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
        if($joueur->getPhoto()!=null){
            $licencier->setNomPhoto($joueur->getPhoto()->getNom());
            $licencier->setPhoto($joueur->getPhoto()->getNom());
        }
        //dd($joueur->getPhoto()->getNom());
        
        return $licencier;
    }
    
}
