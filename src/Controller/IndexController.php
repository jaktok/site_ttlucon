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
     */
    public function index(ArticlesRepository $articleRepo,int $page=1): Response
    {
        
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
        $actualites = $this->api->getActualites();
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
        ]);
    }
}
