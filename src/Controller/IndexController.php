<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FFTTApi;
use FFTTApi\Model\Equipe;
use FFTTApi\Model\Rencontre\RencontreDetails;

class IndexController extends AbstractController
{

    /**
     *
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
       
        // R�cup�ration info config.ini
        $ini_array = parse_ini_file("../config/config.ini");
        $idFftt = $ini_array['fftt_id'];
        $passFftt = $ini_array['fftt_password'];
        $api = new FFTTApi\FFTTApi($idFftt, $passFftt);
 
        /*$actualites = $api->getActualites();
        $joueurByLicence = $api->getClassementJoueurByLicence("852112");
        $lienDivision = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6489&D1=3714&virtuel=0";
        $lienDivisionR = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=1012&cx_poule=3123&D1=3013&virtuel=0";
        $pouleByLien = $api->getClassementPouleByLienDivision($lienDivision);
        $pouleByLienR = $api->getClassementPouleByLienDivision($lienDivisionR);
        $rencontrePouleByLienDiv = $api->getRencontrePouleByLienDivision($lienDivision);
        $rencontrePouleByLienDivR = $api->getRencontrePouleByLienDivision($lienDivisionR);
        $clubDetail = $api->getClubDetails("12850097");
        $equipe = new Equipe("LUCON TT", "D1", $lienDivision);
        $clubEquipe = $api->getClubEquipe($equipe);
        $clubByDepartement = $api->getClubsByDepartement(85);
        $clubByName = $api->getClubsByName("LUCON TT");
        
        // on doit passer en param�tre le lien renvoy� par le r�sultat de $rencontrePouleByLienDiv + id club 1 + id club 2
        $lienRencontre = "is_retour=0&phase=1&res_1=11&res_2=9&renc_id=3785251&equip_1=COEX+1&equip_2=LUCON+2&equip_id1=478822&equip_id2=478827";
        // exemple departement
        $detailRencontreByLien = $api->getDetailsRencontreByLien($lienRencontre,"12850097","12850030");
        // exemple pour r�gion 4 joueurs
        $lienRencontreR = "is_retour=0&phase=1&res_1=3&res_2=11&renc_id=3764848&equip_1=MONTAGNE+%28LA%29++1&equip_2=CHALLANS+2&equip_id1=473709&equip_id2=473716";
        $detailRencontreByLienR = $api->getDetailsRencontreByLien($lienRencontreR,"12440094","12850026");
        $equipesByClub = $api->getEquipesByClub("12850097","M");
        $histoJoueurByLicence = $api->getHistoriqueJoueurByLicence("852112");
        $detailJoueurByLicence = $api->getJoueurDetailsByLicence("852112");
        $joueurByClub = $api->getJoueursByClub("12850097");
        $joueurByNom = $api->getJoueursByNom("pupin","Jean-marie");
        $organismes = $api->getOrganismes();
        $partieJoueurByLicence = $api->getPartiesJoueurByLicence("852112");
        $lienDivision2 = "http://www.fftt.com/sportif/chpt_equipe/chp_div.php?organisme_pere=85&cx_poule=6433&D1=3703&virtuel=0";
        $equipe2 = new Equipe("LUCON 1", "PR", $lienDivision2);
        $prochaineRencontreEquipe = $api->getProchainesRencontresEquipe($equipe2);
        
        $unvalidatePartiesByJoueur = $api->getUnvalidatedPartiesJoueurByLicence("267813");
        $virtualPoints = $api->getVirtualPoints("267813");
        
        dd($actualites, $joueurByLicence, $pouleByLien, $pouleByLienR, $rencontrePouleByLienDiv, $rencontrePouleByLienDivR , $clubDetail, $clubEquipe, $clubByDepartement, $clubByName, $detailRencontreByLien , $detailRencontreByLienR ,$equipesByClub, $histoJoueurByLicence, $detailJoueurByLicence, $joueurByClub, $joueurByNom,$organismes,$partieJoueurByLicence, $prochaineRencontreEquipe, $unvalidatePartiesByJoueur,$virtualPoints);
        */
        //dd($detailRencontreByLien);
        
        /* return $this->render('index/index.html.twig', [
          'controller_name' => 'IndexController',
          ]);*/
        
        $clubDetail = $api->getClubDetails($ini_array['id_club_lucon']);
        $actualites = $api->getActualites();
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'detail_club' => $clubDetail,
            'actus_club' => $actualites
        ]);
    }
}
