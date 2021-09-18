<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi;
use FFTTApi\Model\Equipe;
use FFTTApi\Model\Rencontre\RencontreDetails;
use App\Repository\ArticlesRepository;
use phpDocumentor\Reflection\Types\Array_;
use App\Repository\PartenaireRepository;
use App\Repository\DocAccueilRepository;

class IndexController extends AbstractController
{

    
    private $ini_array;
    private $api;
    
    public function __construct()
    {
        // Recuperation infos config.ini
        $this->ini_array = parse_ini_file("../config/config.ini");
        $this->api = new FFTTApi\FFTTApi();
        
        
    }
    
    /**
     *
     * @Route("/index/{page}", name="index")
     * @Route("/")
     */
    public function index(ArticlesRepository $articleRepo,PartenaireRepository $partenaireRepo, DocAccueilRepository $docAccueilRepo, int $page=1): Response
    {
        
        // recuperation de tous les parenaire
        $listePartenaires = $partenaireRepo->findByActif();
        
        // recuperation de tout les docs accueil
        $listeDocs = $docAccueilRepo->findByActif();
        
        
        // creation  tab doc pour gerer les extensions
        $tabDocs = array();
        $i = 0;
        foreach ($listeDocs as $doc){
            $nomFichier = $doc->getFichier()->getNom();
            $tabNmFic = explode ('.',$nomFichier);
            $extention = $tabNmFic[sizeof($tabNmFic)-1];
            $tabDocs[$i]['libelle'] = $doc->getLibelle();
            $tabDocs[$i]['nomFichier'] = $doc->getFichier()->getNom();
            
            switch ($extention) {
                case "xlsx":
                    $tabDocs[$i]['extention'] = "excel.png";
                    break;
                case "docx":
                    $tabDocs[$i]['extention'] = "word.png";
                    break;
                case "xls":
                    $tabDocs[$i]['extention'] = "excel.png";
                    break;
                case "doc":
                    $tabDocs[$i]['extention'] = "word.png";
                    break;
                case "pdf":
                    $tabDocs[$i]['extention'] = "icone_pdf.png";
                    break;
                case "jpg":
                    $tabDocs[$i]['extention'] = "jpg";
                    break;
                case "jpeg":
                    $tabDocs[$i]['extention'] = "jpg";
                    break;
                case "gif":
                    $tabDocs[$i]['extention'] = "jpg";
                    break;
                case "png":
                    $tabDocs[$i]['extention'] = "jpg";
                    break;
                default:
                    $tabDocs[$i]['extention'] = "icone_base.jpg";
                 break;
            }
            $i++;
        }
        
        $nbArticlesPage = $this->ini_array['nb_articles_page'];
        $url = 'index';
        // recuperation de tous les articles qui ont le tag en ligne (true)
        $listeArticles = $articleRepo->findByEnLigne(true);
       // tableau cree pour paginer
        $tabTabArticles = array();
        $tabArticles = array();
        $i=0;
        $j=1;
        $cmptArt = 0;
        foreach ($listeArticles as $article){
        
            if($cmptArt<$nbArticlesPage){
                $tabArticles[$cmptArt] = $article;
                $cmptArt++;
            }
            else{
                $tabTabArticles[$j] = $tabArticles;
                $tabArticles = array();
                $tabArticles[$cmptArt] = $article;
                $cmptArt = 1;
                
                $j++;
            }
        }
        if ($cmptArt!=0){
            $tabTabArticles[$j] = $tabArticles;
        }
        //dd($tabTabArticles);
        
        $clubDetail = $this->api->getClubDetails($this->ini_array['id_club_lucon']);
       // dd($clubDetail);
        $actualites = array();
        $actualites = $this->api->getActualites();
        
        
        
        
        /*$actualites = $this->api->getActualites();
         $joueurByLicence = $this->api->getClassementJoueurByLicence("852112");
         $lienDivision = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6489&D1=3714&virtuel=0";
         $lienDivisionR = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=1012&cx_poule=3123&D1=3013&virtuel=0";
         $pouleByLien = $this->api->getClassementPouleByLienDivision($lienDivision);
         $pouleByLienR = $this->api->getClassementPouleByLienDivision($lienDivisionR);
         $rencontrePouleByLienDiv = $this->api->getRencontrePouleByLienDivision($lienDivision);
         $rencontrePouleByLienDivR = $this->api->getRencontrePouleByLienDivision($lienDivisionR);
         $clubDetail = $this->api->getClubDetails("12850097");
         $equipe = new Equipe("LUCON TT", "D1", $lienDivision);
         $clubEquipe = $this->api->getClubEquipe($equipe);
         $clubByDepartement = $this->api->getClubsByDepartement(85);
         $clubByName = $this->api->getClubsByName("LUCON TT");
         
         // on doit passer en parametre le lien renvoye par le resultat de $rencontrePouleByLienDiv + id club 1 + id club 2
         $lienRencontre = "is_retour=0&phase=1&res_1=11&res_2=9&renc_id=3785251&equip_1=COEX+1&equip_2=LUCON+2&equip_id1=478822&equip_id2=478827";
         // exemple departement
         $detailRencontreByLien = $this->api->getDetailsRencontreByLien($lienRencontre,"12850097","12850030");
         // exemple pour region 4 joueurs
         $lienRencontreR = "is_retour=0&phase=1&res_1=3&res_2=11&renc_id=3764848&equip_1=MONTAGNE+%28LA%29++1&equip_2=CHALLANS+2&equip_id1=473709&equip_id2=473716";
         $detailRencontreByLienR = $this->api->getDetailsRencontreByLien($lienRencontreR,"12440094","12850026");
         $equipesByClub = $this->api->getEquipesByClub("12850097","M");
         $histoJoueurByLicence = $this->api->getHistoriqueJoueurByLicence("852112");
         $detailJoueurByLicence = $this->api->getJoueurDetailsByLicence("852112");*/
        //$joueurByClub = $this->api->getJoueursByClub("12850097");
        /*$joueurByNom = this->$this->api->getJoueursByNom("pupin","Jean-marie");
         $organismes = $this->api->getOrganismes();
         $partieJoueurByLicence = $this->api->getPartiesJoueurByLicence("852112");
         $lienDivision2 = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6433&D1=3703&virtuel=0";
         $equipe2 = new Equipe("LUCON 1", "PR", $lienDivision2);
         $prochaineRencontreEquipe = $this->api->getProchainesRencontresEquipe($equipe2);
         
         $unvalidatePartiesByJoueur = $this->api->getUnvalidatedPartiesJoueurByLicence("267813");
         $virtualPoints = $this->api->getVirtualPoints("267813");
         
         dd($actualites, $joueurByLicence, $pouleByLien, $pouleByLienR, $rencontrePouleByLienDiv, $rencontrePouleByLienDivR , $clubDetail, $clubEquipe, $clubByDepartement, $clubByName, $detailRencontreByLien , $detailRencontreByLienR ,$equipesByClub, $histoJoueurByLicence, $detailJoueurByLicence, $joueurByClub, $joueurByNom,$organismes,$partieJoueurByLicence, $prochaineRencontreEquipe, $unvalidatePartiesByJoueur,$virtualPoints);
         */
        //dd($detailRencontreByLien);
        
        /* return $this->render('index/index.html.twig', [
         'controller_name' => 'IndexController',
         ]);*/
        
       // $clubDetail = $this->api->getClubDetails($this->ini_array['id_club_lucon']); ok
       // $actualites = $this->api->getActualites(); ok 
        //$joueurByLicence = $this->api->getClassementJoueurByLicence("852112"); ok
        // $joueurByClub = $this->api->getJoueursByClub("12850097"); OK
        $lienDivision = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6489&D1=3714&virtuel=0";
       // $pouleByLien = $this->api->getClassementPouleByLienDivision($lienDivision); ko avec ce lien
        //$clubDetail = $this->api->getClubDetails("12850097"); ok
        $equipe = new Equipe("LUCON TT", "D1", $lienDivision);
       // $clubEquipe = $this->api->getClubEquipe($equipe);
        //$clubByDepartement = $this->api->getClubsByDepartement(85);ok
        //$equipesByClub = $this->api->getEquipesByClub("12850097","M");ok
       // $histoJoueurByLicence = $this->api->getHistoriqueJoueurByLicence("852112");ok
        //$partieJoueurByLicence = $this->api->getPartiesJoueurByLicence("852112"); ok apres modif partie
       //  $unvalidatePartiesByJoueur = $this->api->getUnvalidatedPartiesJoueurByLicence("267813"); ko mais non utilisee ....
       // $virtualPoints = $this->api->getVirtualPoints("267813"); ko (appelle la fonction du dessus ) mais non utilisee ....
        //dd($virtualPoints);
        
        
        
        
        
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'detail_club' => $clubDetail,
            'actus_club' => $actualites,
            'tabTabArticles' => $tabTabArticles,
            'page'  => $page,
            'currentPage'  => $page,
            'current'  => $page,
            'nbPages' => $j,
            'url' => $url,
            'listePartenaires' => $listePartenaires,
            'tabDocs' => $tabDocs,
        ]);
    }
}
