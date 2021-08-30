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
        $actualites = array();
        //$actualites = $this->api->getActualites();
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
