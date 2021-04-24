<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi;

class IndexController extends AbstractController
{

    /**
     *
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        $api = new FFTTApi\FFTTApi("SW624", "93hUQWRcr6");
 
        $actualites = $api->getActualites();
        $joueurByLicence = $api->getClassementJoueurByLicence("852112");
        $lienDivision = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6489&D1=3714&virtuel=0";
        $pouleByLien = $api->getClassementPouleByLienDivision($lienDivision);
        $clubDetail = $api->getClubDetails("12850097");
        // $clubEquipe = $api->getClubEquipe(478827);
        $clubByDepartement = $api->getClubsByDepartement(85);
        $clubByName = $api->getClubsByName("LUCON TT");
        // $detailRencontreByLien = $api->getDetailsRencontreByLien($lienRencontre);
        // $equipesByClub = $api->getEquipesByClub("12850097");
        $histoJoueurByLicence = $api->getHistoriqueJoueurByLicence("852112");
        $detailJoueurByLicence = $api->getJoueurDetailsByLicence("852112");
        $joueurByClub = $api->getJoueursByClub("12850097");
        $joueurByNom = $api->getJoueursByNom("pupin","Jean-marie");
        $organismes = $api->getOrganismes();
        $partieJoueurByLicence = $api->getPartiesJoueurByLicence("852112");
        //$prochaineRencontreEquipe = $api->getProchainesRencontresEquipe("478827");
        $rencontrePouleByLienDiv = $api->getRencontrePouleByLienDivision($lienDivision);
        $unvalidatePartiesByJoueur = $api->getUnvalidatedPartiesJoueurByLicence("852112");
        $virtualPoints = $api->getVirtualPoints("852112");

        dd($actualites, $joueurByLicence, $pouleByLien, $clubDetail, "getClubEquipe ko", $clubByDepartement, $clubByName, "getDetailsRencontreByLien ko", " equipesByClub ko", $histoJoueurByLicence, $detailJoueurByLicence, $joueurByClub, $joueurByNom,$organismes,$partieJoueurByLicence," getProchainesRencontresEquipe(478827) ko ",$rencontrePouleByLienDiv,$unvalidatePartiesByJoueur,$virtualPoints);

        /*
         * return $this->render('index/index.html.twig', [
         * 'controller_name' => 'IndexController',
         * ]);
         */
    }
}
