<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi\FFTTApi;
use App\Repository\JoueursRepository;
use App\Repository\MatchsRepository;

class StatistiqueController extends AbstractController
{
    
    private $ini_array;
    private $api;
    private $idClub;
    
    public function __construct()
    {
        // Recuperation infos config.ini
        $this->ini_array = parse_ini_file("../config/config.ini");
        $this->api = new FFTTApi();
        $this->idClub = $this->ini_array['id_club_lucon'];
    }
    
    /**
     * @Route("/statistique{cat}", name="statistique")
     */
    public function index(JoueursRepository $joueursRepo,MatchsRepository $matchsRepo, $cat=null): Response
    {
        
        // recup liste des joueurs du club
        $tabJoueurByClub = $this->api->getJoueursByClub($this->idClub);
        
        $tabJoueursLucon = array();
        $i=0;
    if ($cat){
        // on parcourt le tableau pour associer avec les joueurs
        foreach ($tabJoueurByClub as $joueurs){
            $noLicence = $joueurs->getLicence();
            // on recupere le joueur chez nous
            $joueurTTL = $joueursRepo->findOneByLicenceActif($noLicence);
            if ($joueurTTL!=null){
                $categorie = $joueurTTL->getCategories()->getLibelle();
                if (($cat==$categorie || $cat=="tous") && $categorie!='Loisir'){
                $tabJoueursLucon[ $i]['joueur'] = $joueurTTL;
                // on va chercher le classement du joueur
                $joueurByLicence = $this->api->getClassementJoueurByLicence($noLicence);
                $pointsDebutSaison = $joueurByLicence->getPointsInitials();
                $pointsActuel = $joueurByLicence->getPoints();
                $pointsMoisDernier = $joueurByLicence->getAnciensPoints();
                $progressionAnnuelle = $pointsActuel - $pointsDebutSaison;
                $progressionMensuelle = $pointsActuel - $pointsMoisDernier;
                $tabJoueursLucon[ $i]['pointsDebutSaison'] = round($pointsDebutSaison);
                $tabJoueursLucon[ $i]['pointsActuel'] = round($pointsActuel);
                $tabJoueursLucon[ $i]['pointsMoisDernier'] = round($pointsMoisDernier);
                $tabJoueursLucon[ $i]['progressionAnnuelle'] = round($progressionAnnuelle);
                $tabJoueursLucon[ $i]['progressionMensuelle'] = round($progressionMensuelle);
                $tabJoueursLucon[ $i]['rangDep'] = $joueurByLicence->getRangDepartemental();
                $tabJoueursLucon[ $i]['rangReg'] = $joueurByLicence->getRangRegional();
                $tabJoueursLucon[ $i]['rangNat'] = $joueurByLicence->getRangNational();
                // on va chercher les parties du joueur
                $partieJoueurByLicence = $this->api->getPartiesJoueurByLicence($noLicence);
                if ($partieJoueurByLicence){
                    $vict = 0;
                    $def = 0;
                   // on parcours le tab pour compter les victoires et les defaites
                    foreach ($partieJoueurByLicence as $partie){
                        if($partie->isIsVictoire()){
                            $vict++;
                        }
                        else{
                            $def++;
                        }
                    }
                    $tabJoueursLucon[ $i]['victoires'] = $vict;
                    $tabJoueursLucon[ $i]['defaites'] = $def;
                    if ($vict+$def != 0){
                        $tabJoueursLucon[ $i]['pourcent'] = round(($vict*100)/($vict+$def));
                    }
                    else{
                        $tabJoueursLucon[ $i]['pourcent'] = 0;
                    }
                }
                else{
                    $tabJoueursLucon[ $i]['victoires'] = 0;
                    $tabJoueursLucon[ $i]['defaites'] = 0;
                    $tabJoueursLucon[ $i]['pourcent'] = 0;
                }
                // on va chercher les doubles
                $doubles = $matchsRepo->findDoublesByIdJoueur($joueurTTL->getId());
                
                $victDouble = 0;
                $defDouble = 0;
               foreach ($doubles as $double){
                    if($double->getVictoire()){
                        $victDouble++;
                    }
                    else{
                        $defDouble++;
                    }
                }
                $tabJoueursLucon[ $i]['victoiresDouble'] = $victDouble;
                $tabJoueursLucon[ $i]['defaitesDouble'] = $defDouble;
                if ($victDouble+$defDouble != 0){
                    $tabJoueursLucon[ $i]['pourcentDouble'] = round(($victDouble*100)/($victDouble+$defDouble));
                }
                else{
                    $tabJoueursLucon[ $i]['pourcentDouble'] = 0;
                }
            }
            
            }
            $i++;
        }
     }
        // tri du tableau par progression
        $progressionAnnuelle = array_column($tabJoueursLucon, 'progressionAnnuelle');
        array_multisort($progressionAnnuelle, SORT_DESC, $tabJoueursLucon);
        return $this->render('statistique/statistique.html.twig', [
            'tabJoueursTTL' => $tabJoueursLucon,
            'categorie' => $cat,
        ]);
    }
}
