<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi\FFTTApi;
use App\Repository\JoueursRepository;
use App\Repository\MatchsRepository;
use App\Repository\CompetitionRepository;

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
     *
     * @Route("/statistique{cat}", name="statistique")
     */
    public function index(JoueursRepository $joueursRepo, MatchsRepository $matchsRepo,CompetitionRepository $competitionRepo  ,$cat = null): Response
    {

        // recup liste des joueurs du club
        $tabJoueurByClub = $this->api->getLicenceJoueursByClub($this->idClub);
        $tabJoueursLucon = array();
        $i = 0;
        if ($cat) {
            // on parcourt le tableau pour associer avec les joueurs
            foreach ($tabJoueurByClub as $noLicence) {
                // on recupere le joueur chez nous
                $joueurTTL = $joueursRepo->findOneByLicenceActif($noLicence);
                if ($joueurTTL != null && ($joueurTTL->getVictoires()+$joueurTTL->getDefaites()>0)) {
                        $categorie = $joueurTTL->getCategories()->getLibelle();
                        if (($cat == $categorie || $cat == "tous") && $categorie != 'Loisir') {
                      //      $partieJoueurByLicence = $this->api->getPartiesParLicenceStats($noLicence);
                          //      if ($partieJoueurByLicence) {
                                $tabJoueursLucon[$i]['joueur'] = $joueurTTL;
                                // on va chercher le classement du joueur
                                //$joueurByLicence = $this->api->getClassementJoueurByLicence($noLicence);
                                $pointsDebutSaison = $joueurTTL->getPointsDebSaison();
                                $pointsActuel = $joueurTTL->getPointsActuel();
                                $pointsMoisDernier = $joueurTTL->getPointsMoisDernier();
                                $progressionAnnuelle = $pointsActuel - $pointsDebutSaison;
                                $progressionMensuelle = $pointsActuel - $pointsMoisDernier;
                                $tabJoueursLucon[$i]['pointsDebutSaison'] = round($pointsDebutSaison);
                                $tabJoueursLucon[$i]['pointsActuel'] = round($pointsActuel);
                                $tabJoueursLucon[$i]['pointsMoisDernier'] = round($pointsMoisDernier);
                                $tabJoueursLucon[$i]['progressionAnnuelle'] = round($progressionAnnuelle);
                                $tabJoueursLucon[$i]['progressionMensuelle'] = round($progressionMensuelle);
                                $tabJoueursLucon[$i]['rangDep'] = $joueurTTL->getRangDep();
                                $tabJoueursLucon[$i]['rangReg'] = $joueurTTL->getRangReg();
                                $tabJoueursLucon[$i]['rangNat'] = $joueurTTL->getRangNat();
                                
                                $tabJoueursLucon[$i]['victoires'] = $joueurTTL->getVictoires();
                                $tabJoueursLucon[$i]['defaites'] = $joueurTTL->getDefaites();
                                $tabJoueursLucon[$i]['pourcent'] = round(($tabJoueursLucon[$i]['victoires'] * 100) / ($tabJoueursLucon[$i]['victoires'] + $tabJoueursLucon[$i]['defaites']));
         
                                // on va chercher les doubles
                                $doubles = $matchsRepo->findDoublesByIdJoueur($joueurTTL->getId());
                                $victDouble = 0;
                                $defDouble = 0;
                                foreach ($doubles as $double) {
                                    if ($double->getVictoire()) {
                                        $victDouble ++;
                                    } else {
                                        $defDouble ++;
                                    }
                                }
                                $tabJoueursLucon[$i]['victoiresDouble'] = $victDouble;
                                $tabJoueursLucon[$i]['defaitesDouble'] = $defDouble;
                                if ($victDouble + $defDouble != 0) {
                                    $tabJoueursLucon[$i]['pourcentDouble'] = round(($victDouble * 100) / ($victDouble + $defDouble));
                                } else {
                                    $tabJoueursLucon[$i]['pourcentDouble'] = 0;
                                }
                         //   } // fin if partiesjoueurbylicence
                        } // fin if categorie
                } // fin if joueur
                $i ++;
            }
        }
        // tri du tableau par progression
        $progressionAnnuelle = array_column($tabJoueursLucon, 'progressionAnnuelle');
        array_multisort($progressionAnnuelle, SORT_DESC, $tabJoueursLucon);
        
        
        $pos = 1;
        for($i=0; $i < sizeof($tabJoueursLucon); $i++){
            if($i==0){
                $tabJoueursLucon[$i]['position'] = $pos;
            }
            if ($i!=0 && $tabJoueursLucon[$i]['progressionAnnuelle'] ==  $tabJoueursLucon[$i-1]['progressionAnnuelle']){
                $tabJoueursLucon[$i]['position'] = $tabJoueursLucon[$i-1]['position'];
            }
            else if ($i!=0 && $tabJoueursLucon[$i]['progressionAnnuelle'] !=  $tabJoueursLucon[$i-1]['progressionAnnuelle']){
                $tabJoueursLucon[$i]['position'] = $pos;
            }
            $pos++;
        }

        $tabJoueursAdapte = array();
        if ($cat=="Adapte"){
            $listeIdCompetitionsAutreAdapte = $competitionRepo->findIdAdapte();
            $matchsAdapteTTL = $matchsRepo->findOneByLicenceAdapte($listeIdCompetitionsAutreAdapte);
           //dd($matchsAdapteTTL);
           $joueurAdapteTTL = $joueursRepo->findByLicenceAdapte();
           $i = 0;
           foreach ($joueurAdapteTTL as $joueurAdapte) {
               $vict = 0;
               $def = 0;
               foreach ($matchsAdapteTTL as $matchAdapte){
                   if ($matchAdapte->getJoueur()->getId() == $joueurAdapte->getId()){
                       if($matchAdapte->getVictoire()){
                           $vict += 1;
                       }
                       else{
                           $def += 1;
                       }
                   }
               }
               if ($vict + $def > 0){
               $tabJoueursAdapte[$i]['joueur'] = $joueurAdapte;
               $tabJoueursAdapte[$i]['vict'] = $vict;
               $tabJoueursAdapte[$i]['def'] = $def;
               $tabJoueursAdapte[$i]['pourcent'] = round(($vict * 100) / ($vict + $def));
               $i++;
               }
            }
               
        } 
           $pourcent = array_column($tabJoueursAdapte, 'pourcent');
           array_multisort($pourcent, SORT_DESC, $tabJoueursAdapte);
         
           $pos = 1;
           for($i=0; $i < sizeof($tabJoueursAdapte); $i++){
               if($i==0){
                   $tabJoueursAdapte[$i]['position'] = $pos;
               }
               if ($i!=0 && $tabJoueursAdapte[$i]['pourcent'] ==  $tabJoueursAdapte[$i-1]['pourcent']){
                   $tabJoueursAdapte[$i]['position'] = $tabJoueursAdapte[$i-1]['position'];
               }
               else if ($i!=0 && $tabJoueursAdapte[$i]['pourcent'] !=  $tabJoueursAdapte[$i-1]['pourcent']){
                   $tabJoueursAdapte[$i]['position'] = $pos;
               }
               $pos++;
           }
         
         
         //  dd ($tabJoueursAdapte);
        
        return $this->render('statistique/statistique.html.twig', [
            'tabJoueursTTL' => $tabJoueursLucon,
            'categorie' => $cat,
            'tabAutresCompetAdapte' => $tabJoueursAdapte,
        ]);
    }
}
