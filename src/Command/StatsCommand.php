<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use FFTTApi\FFTTApi;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\JoueursRepository;

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

    public function __construct(EntityManagerInterface $manager,JoueursRepository $joueurRepo)
    {
        $this->manager = $manager;
        $this->joueurRepo = $joueurRepo;
        $this->api = new FFTTApi();
        $this->idClub ="12850097";
        parent::__construct(self::$defaultName);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
          $io = new SymfonyStyle($input, $output);
        //dd();
        // recup liste des joueurs du club
        $tabJoueurByClub = $this->api->getLicenceJoueursByClub($this->idClub);
        $tabJoueursLucon = array();
        $i = 0;
        // on parcourt le tableau pour associer avec les joueurs
        foreach ($tabJoueurByClub as $noLicence) {
            // on recupere le joueur chez nous
            $joueurTTL = $this->joueurRepo->findOneByLicenceActif($noLicence);
            if ($joueurTTL != null) {
                $partieJoueurByLicence = $this->api->getPartiesParLicenceStats($noLicence);
                if ($partieJoueurByLicence) {
                    $tabJoueursLucon[$i]['joueur'] = $joueurTTL;
                    // on va chercher le classement du joueur
                    $joueurByLicence = $this->api->getClassementJoueurByLicence($noLicence);
                    $pointsDebutSaison = $joueurByLicence->getPointsInitials();
                    $pointsActuel = $joueurByLicence->getPoints();
                    $pointsMoisDernier = $joueurByLicence->getAnciensPoints();
                    $joueurTTL->setPointsDebSaison(round($pointsDebutSaison));
                    $joueurTTL->setPointsActuel( round($pointsActuel));
                    $joueurTTL->setPointsMoisDernier(round($pointsMoisDernier));
                    $joueurTTL->setRangDep($joueurByLicence->getRangDepartemental());
                    $joueurTTL->setRangReg($joueurByLicence->getRangRegional());
                    $joueurTTL->setRangNat($joueurByLicence->getRangNational());
                    $joueurTTL->setVictoires($partieJoueurByLicence["vict"]);
                    $joueurTTL->setDefaites($partieJoueurByLicence["def"]);
                } // fin if partiesjoueurbylicence
                // on enregistre les données joueur
                $this->manager->persist($joueurTTL);
                $this->manager->flush();
            } // fin if joueur
            // on enregistre les données joueur
            $i ++;
        }

        return Command::SUCCESS;
    }
}
