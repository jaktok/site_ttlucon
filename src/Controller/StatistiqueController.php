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
     *
     * @Route("/statistique{cat}", name="statistique")
     */
    public function index(JoueursRepository $joueursRepo, MatchsRepository $matchsRepo, $cat = null): Response
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
        $tabAutresCompetAdapte = array();
        if ($cat=="Adapte"){
            /**@TODO gerer des stats uniquement sur les tournois sport adapte */
        }
        
        return $this->render('statistique/statistique.html.twig', [
            'tabJoueursTTL' => $tabJoueursLucon,
            'categorie' => $cat,
            'tabAutresCompetAdapte' => $tabAutresCompetAdapte,
        ]);
    }
}
