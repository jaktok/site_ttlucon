<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use FFTTApi\FFTTApi;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\JoueursRepository;
use App\Repository\MatchsRepository;

class StatsCommand extends Command
{
    protected static $defaultName = 'StatsCommand';
    private $api;
    private $idClub;
    private $joueurRepo;
    private $manager;

    protected function configure(): void
    {
        $this->setDescription('mise a jour des infos joueurs');
    }

    public function __construct(EntityManagerInterface $manager,JoueursRepository $joueurRepo,MatchsRepository $matchsRepo)
    {
        $this->manager = $manager;
        $this->joueurRepo = $joueurRepo;
        $this->matchsRepo = $matchsRepo;
        $this->api = new FFTTApi();
        $this->idClub ="12850097";
        parent::__construct(self::$defaultName);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
          $io = new SymfonyStyle($input, $output);
        
          // tableau cree si fftt n est pas capable de ramener les resultats en cours ...
          $tabResultLocal = array();
          // resuperation saison suite a gestion calamiteuse des parties fftt ....
          $annee = date("Y");
          $year = date("Y");
          $month = date("m");
          if($month <= 8){
              $annee = $year - 1;
          }
          $mois = "8";
          
          $phase = 2;
          $anneeEncours = date("Y");
          $moisEncours = date("m");
          $tabPhase1 = array("08","09","10","11","12");
          $tabPhase2 = array("01","02","03","04","05","06","07");
          if (in_array($moisEncours, $tabPhase1)){
              $phase = 1;
          }
          if (in_array($moisEncours, $tabPhase2)){
              $phase = 2;
              $anneeEncours -= $anneeEncours;
          }
          
        // recup liste des joueurs du club sur FFTT
        $tabJoueurByClub = $this->api->getLicenceJoueursByClub($this->idClub);
        // on parcourt le tableau pour associer avec les joueurs enregistrés 
        foreach ($tabJoueurByClub as $noLicence) {
            // on recupere le joueur chez nous
            $joueurTTL = $this->joueurRepo->findOneByLicenceActif($noLicence);
            if ($joueurTTL != null) {
                $isResultLocal = false;
                $partieJoueurByLicence = $this->api->getPartiesParLicenceStatsSaison($noLicence,$annee,$mois);
                // gestion du classement debut non gere a ce jour par fftt ...
                $histoJoueurByLicence = $this->api->getHistoriqueJoueurByLicence($noLicence);
                $classementDebut = 0;
                foreach ($histoJoueurByLicence as $histo){
                    if($histo->getAnneeDebut() == $anneeEncours  && $histo->getPhase() == $phase){
                        $classementDebut = $histo->getPoints();
                    }
                }
                
                if( (!empty($partieJoueurByLicence) && ($partieJoueurByLicence["vict"]+$partieJoueurByLicence["def"]<=0)) || empty($partieJoueurByLicence)){
                    $tabResultLocal["vict"] = $this->matchsRepo->findVictoiresByIdJoueur($joueurTTL->getId());
                    $tabResultLocal["def"] = $this->matchsRepo->findDefaitesByIdJoueur($joueurTTL->getId());
                    $isResultLocal = true;
                }
                
                    // on va chercher le classement du joueur
                    $joueurByLicence = $this->api->getClassementJoueurByLicence($noLicence);
                    $pointsDebutSaison = $joueurByLicence->getPointsInitials();
                    $pointsActuel = $joueurByLicence->getPoints();
                    $pointsMoisDernier = $joueurByLicence->getAnciensPoints();
                    if($classementDebut!=0){
                        $joueurTTL->setPointsDebSaison(round($classementDebut));
                    }
                    else{
                        $joueurTTL->setPointsDebSaison(round($pointsDebutSaison));
                    }
                    $joueurTTL->setPointsActuel( round($pointsActuel));
                    $joueurTTL->setPointsMoisDernier(round($pointsMoisDernier));
                    $joueurTTL->setRangDep($joueurByLicence->getRangDepartemental());
                    $joueurTTL->setRangReg($joueurByLicence->getRangRegional());
                    $joueurTTL->setRangNat($joueurByLicence->getRangNational());
                    if ($partieJoueurByLicence) {
                    $joueurTTL->setVictoires($partieJoueurByLicence["vict"]);
                    $joueurTTL->setDefaites($partieJoueurByLicence["def"]);
                    } // fin if partiesjoueurbylicence
                    if($isResultLocal){
                        $joueurTTL->setVictoires($tabResultLocal["vict"]);
                        $joueurTTL->setDefaites($tabResultLocal["def"]);
                    }
                
                // on enregistre les données joueur
                $this->manager->persist($joueurTTL);
                $this->manager->flush();
            } // fin if joueur
        }

        return Command::SUCCESS;
    }
}
