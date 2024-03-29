<?php
/**
 * Created by Antoine Lamirault.
 */

namespace FFTTApi;

use Accentuation\Accentuation;
use FFTTApi\Exception\ClubNotFoundException;
use FFTTApi\Exception\InvalidLienRencontre;
use FFTTApi\Exception\JoueurNotFound;
use FFTTApi\Model\Actualite;
use FFTTApi\Model\Classement;
use FFTTApi\Model\ClubDetails;
use FFTTApi\Model\Equipe;
use FFTTApi\Model\EquipePoule;
use FFTTApi\Model\Historique;
use FFTTApi\Model\Joueur;
use FFTTApi\Model\JoueurDetails;
use FFTTApi\Model\Organisme;
use FFTTApi\Model\Partie;
use FFTTApi\Model\Club;
use FFTTApi\Exception\InvalidCredidentials;
use FFTTApi\Exception\NoFFTTResponseException;
use FFTTApi\Model\Rencontre\Rencontre;
use FFTTApi\Model\Rencontre\RencontreDetails;
use FFTTApi\Service\ClubFactory;
use FFTTApi\Service\PointCalculator;
use FFTTApi\Service\RencontreDetailsFactory;
use FFTTApi\Model\UnvalidatedPartie;
use FFTTApi\Service\Utils;
use phpDocumentor\Reflection\Types\Null_;
use function Symfony\Component\DependencyInjection\Exception\__toString;
use phpDocumentor\Reflection\Types\Array_;

class FFTTApi
{
    private $id;
    private $password;
    private $apiRequest;

    public function __construct()
    {
        $idFftt = "SW624";
        $passFftt = "93hUQWRcr6";
        $this->id = $idFftt;
        $this->password = md5($passFftt);
        $this->apiRequest = new ApiRequest($this->password, $this->id);
    }

    public function initialize()
    {
        $time = round(microtime(true) * 1000);
        $timeCrypted = hash_hmac("sha1", $time, $this->password);
        $uri = 'http://www.fftt.com/mobile/pxml/xml_initialisation.php?serie=' . $this->id
       // $uri = 'http://apiv2.fftt.com/mobile/pxml/xml_initialisation.php?serie=' . $this->id
            . '&tm=' . $time
            . '&tmc=' . $timeCrypted
            . '&id=' . $this->id;

        $response = $this->apiRequest->send($uri);

        if (!$response) {
            throw new InvalidCredidentials();
        }
        return $response;
    }

    /**
     * @param string $type
     * @return Organisme[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getOrganismes(string $type = "Z"): array
    {
        if (!in_array($type, ['Z', 'L', 'D'])) {
            $type = 'L';
        }

        $organismes = $this->apiRequest->get('xml_organisme', [
            'type' => $type,
        ])["organisme"];

        $result = [];
        foreach ($organismes as $organisme) {
            $result[] = new Organisme(
                $organisme["libelle"],
                $organisme["id"],
                $organisme["code"],
                $organisme["idpere"]
            );
        }

        return $result;
    }

    /**
     * @param int $departementId
     * @return Club[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getClubsByDepartement(int $departementId): array
    {

        $data = $this->apiRequest->get('xml_club_dep2', [
            'dep' => $departementId,
        ])['club'];
        $clubFactory = new ClubFactory();
        return $clubFactory->createFromArray($data);
    }

    /**
     * @param string $name
     * @return Club[]
     */
    public function getClubsByName(string $name)
    {
        try {
            $data = $this->apiRequest->get('xml_club_b', [
                'ville' => $name,
            ])['club'];

            $data = $this->wrappedArrayIfUnique($data);

            $clubFactory = new ClubFactory();
            return $clubFactory->createFromArray($data);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param string $clubId
     * @return ClubDetails
     * @throws ClubNotFoundException
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getClubDetails(string $clubId): ClubDetails
    {
        $clubData = $this->apiRequest->get('xml_club_detail', [
            'club' => $clubId,
        ])['club'];
        if (empty($clubData['numero'])) {
            throw new ClubNotFoundException($clubId);
        }
        return new ClubDetails(
            intval($clubData['numero']),
            $clubData['nom'],
            is_array($clubData['nomsalle']) ? null : $clubData['nomsalle'],
            is_array($clubData['adressesalle1']) ? null : $clubData['adressesalle1'],
            is_array($clubData['adressesalle2']) ? null : $clubData['adressesalle2'],
            is_array($clubData['adressesalle3']) ? null : $clubData['adressesalle3'],
            is_array($clubData['codepsalle']) ? null : $clubData['codepsalle'],
            is_array($clubData['villesalle']) ? null : $clubData['villesalle'],
            is_array($clubData['web']) ? null : $clubData['web'],
            is_array($clubData['nomcor']) ? null : $clubData['nomcor'],
            is_array($clubData['prenomcor']) ? null : $clubData['prenomcor'],
            is_array($clubData['mailcor']) ? null : $clubData['mailcor'],
            is_array($clubData['telcor']) ? null : $clubData['telcor'],
            is_array($clubData['latitude']) ? null : floatval($clubData['latitude']),
            is_array($clubData['longitude']) ? null : floatval($clubData['longitude'])
        );
    }

    /**
     * @param string $clubId
     * @return Joueur[]
     * @throws ClubNotFoundException
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getJoueursByClub(string $clubId): array
    {
        try {
            $arrayJoueurs = $this->apiRequest->getJoueursByClub('xml_liste_joueur_o', [
                    'club' => $clubId,
                ]
            );
        } catch (NoFFTTResponseException $e) {
            throw new ClubNotFoundException($clubId);
        }

        $result = [];

        foreach ($arrayJoueurs['joueur'] as $joueur) {
            $realJoueur = new Joueur(
                $joueur['licence'],
                $joueur['nclub'],
                $joueur['club'],
                $joueur['nom'],
                $joueur['prenom'],
                $joueur['points']);
            $result[] = $realJoueur;
        }
        return $result;
    }

    
    /**
     * @param string $clubId
     * @return Joueur[]
     * @throws ClubNotFoundException
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getLicenceJoueursByClub(string $clubId): array
    {
        try {
            return $this->apiRequest->getLicencesParClub('xml_liste_joueur_o', [
                'club' => $clubId,
            ]
                );
        } catch (NoFFTTResponseException $e) {
            return null;
        }
    }

    /**
     * @param string $nom
     * @param string $prenom
     * @return Joueur[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getJoueursByNom(string $nom, string $prenom = ""): array
    {
        /*$arrayJoueurs = $this->apiRequest->getJoueurByNom('xml_liste_joueur', [
                'nom' => addslashes(Accentuation::remove($nom)),
                'prenom' => addslashes(Accentuation::remove($prenom)),
            ]
        )['joueur'];
*/
        $result = [];
        
               $arrayJoueurs = $this->apiRequest->getJoueurByNom('xml_liste_joueur', [
                'nom' => addslashes(Accentuation::remove($nom)),
                'prenom' => addslashes(Accentuation::remove($prenom)),
            ]
                );
            if ($arrayJoueurs==null){
                $realJoueur = new Joueur(
                    '',
                   '',
                    '',
                    '',
                    '',
                    '');
                $result[] = $realJoueur;
                return array();
            }
            else{
                $arrayJoueurs = $this->apiRequest->getJoueurByNom('xml_liste_joueur', [
                    'nom' => addslashes(Accentuation::remove($nom)),
                    'prenom' => addslashes(Accentuation::remove($prenom)),
                ]
                    )['joueur'];
            }
       
       $arrayJoueurs = $this->wrappedArrayIfUnique($arrayJoueurs);

        foreach ($arrayJoueurs as $joueur) {
            $realJoueur = new Joueur(
                $joueur['licence'],
                $joueur['nclub'],
                $joueur['club'],
                $joueur['nom'],
                $joueur['prenom'],
                $joueur['clast']);
            $result[] = $realJoueur;
        }
        return $result;
    }

    /**
     * @param string $licenceId
     * @return JoueurDetails
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws JoueurNotFound
     */
    public function getJoueurDetailsByLicence(string $licenceId): JoueurDetails
    {
        try {
            
            $data = $this->apiRequest->getJoueurDetail('xml_licence_b', [
                'licence' => $licenceId,
            ]
                );
            if ($data==null){
                return new JoueurDetails($licenceId, "", "", "", "", "", "", "500", "500", "500", "500");
            }
 
        } catch (NoFFTTResponseException $e) {
           
            throw new JoueurNotFound($licenceId);
        }
        $pointsInit = "500";
        $pointsMensuel = "500";
        if($data['initm'] == ""){
            $pointsInit = floatval($data['point']);
            $pointsMensuel = floatval($data['pointm']);
        }
        else{
            $pointsInit = floatval($data['initm']);
            $pointsMensuel = floatval($data['pointm']);
        }
        $joueurDetails = new JoueurDetails(
            $licenceId,
            $data['nom'],
            $data['prenom'],
            $data['numclub'],
            $data['nomclub'],
            $data['sexe'] === 'M' ? true : false,
            $data['cat'],
            $pointsInit,
            floatval($data['point']),
            $pointsMensuel,
            floatval($data['apointm'] ?? floatval($data['point']))
        );
        return $joueurDetails;
    }

    /**
     * @param string $licenceId
     * @return Classement
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws JoueurNotFound
     */
    public function getClassementJoueurByLicence(string $licenceId): Classement
    {
        try {
            $joueurDetails = $this->apiRequest->getClassementDetail('xml_joueur', [
                'licence' => $licenceId,
            ]);
            
            if ($joueurDetails==null){
                return new Classement(new \DateTime(), "0", "0", "0", "0", "0", "0", "0", "0");
            }
            
            
            $joueurDetails = $this->apiRequest->getClassementDetail('xml_joueur', [
                'licence' => $licenceId,
            ])['joueur'];
            
        } catch (NoFFTTResponseException $e) {
            throw new JoueurNotFound($licenceId);
        }

        $classement = new Classement(
            new \DateTime(),
            $joueurDetails['point'],
            $joueurDetails['apoint'],
            intval($joueurDetails['clast']),
            intval($joueurDetails['clnat']),
            intval($joueurDetails['rangreg']),
            intval($joueurDetails['rangdep']),
            intval($joueurDetails['valcla']),
            intval($joueurDetails['valinit'])
        );
        return $classement;
    }

    /**
     * @param string $licenceId
     * @return Historique[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws JoueurNotFound
     */
    public function getHistoriqueJoueurByLicence(string $licenceId): array
    {
        try {
            $classements = $this->apiRequest->getHistoriqueJoueurByLicence('xml_histo_classement', [
                'numlic' => $licenceId,
            ]);
            
            if (empty($classements)){
                return $classements;
            }
            
            $classements = $this->apiRequest->getHistoriqueJoueurByLicence('xml_histo_classement', [
                'numlic' => $licenceId,
            ])['histo'];
            
        } catch (NoFFTTResponseException $e) {
            throw new JoueurNotFound($licenceId);
        }
        $result = [];
        $classements = $this->wrappedArrayIfUnique($classements);

        foreach ($classements as $classement) {
            $explode = explode(' ', $classement['saison']);

            $historique = new Historique($explode[1], $explode[3], intval($classement['phase']), intval($classement['point']));
            $result[] = $historique;
        }

        return $result;
    }

    /**
     * @param string $joueurId
     * @return Partie[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getPartiesJoueurByLicence(string $joueurId): array
    {

        try {
            
            $parties = $this->apiRequest->getPartiesParLicence('xml_partie_mysql', [
                'licence' => $joueurId,
            ]);
            $tabVide = array();
            if ($parties==null){
                return $tabVide;
            }
            
            $parties = $this->apiRequest->getPartiesParLicence('xml_partie_mysql', [
                'licence' => $joueurId,
            ])['partie'];
            $parties = $this->wrappedArrayIfUnique($parties);
        } catch (NoFFTTResponseException $e) {
            $parties = [];
        }
        $res = [];
        foreach ($parties as $partie) {
            list($nom, $prenom) = Utils::returnNomPrenom($partie['advnompre']);
            $date= $partie['date']." 00:00:00";
            $dateTime = str_replace("/", "-", $date);
            $dt = \DateTime::createFromFormat("d-m-y H:i:s", $dateTime);
            $realPartie = new Partie(
                $partie["vd"] === "V" ? true : false,
                intval($partie['numjourn']),
                //\DateTime::createFromFormat('d/m/y', $partie['date']),
                $partie['date'],
                floatval($partie['pointres']),
                floatval($partie['coefchamp']),
                $partie['advlic'],
                $partie['advsexe'] === 'M' ? true : false,
                $nom,
                $prenom,
                intval($partie['advclaof'])
                );
            $res[] = $realPartie;
        }
        return $res;
    }

    /**
     * @param string $joueurId
     * @return Partie[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getPartiesParLicenceStats(string $joueurId): array
    {
        
        try {
            
            $parties = $this->apiRequest->getPartiesParLicenceStats('xml_partie_mysql', [
                'licence' => $joueurId,
            ]);
            
            $tabVide = array();
            if ($parties==null){
                return $tabVide;
            }
            else{
                return $parties;
            }
        } catch (NoFFTTResponseException $e) {
            $parties = [];
        }
    }
    

    /**
     * @param string $joueurId
     * @return Partie[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getPartiesParLicenceStatsSaison(string $joueurId,string $annee,string $mois): array
    {
        
        try {
            
            $parties = $this->apiRequest->getPartiesParLicenceStatsSaison('xml_partie_mysql', [
                'licence' => $joueurId
            ],$annee,$mois);
            
            $tabVide = array();
            if ($parties==null){
                return $tabVide;
            }
            else{
                return $parties;
            }
        } catch (NoFFTTResponseException $e) {
            $parties = [];
        }
    }
    
    /**
     * @param string $joueurId
     * @return UnvalidatedPartie[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getUnvalidatedPartiesJoueurByLicence(string $joueurId): array
    {
        $validatedParties = $this->getPartiesJoueurByLicence($joueurId);
        try {
            $allParties = $this->apiRequest->getUnvalidatedPartiesJoueurByLicence('xml_partie', [
                'numlic' => $joueurId,
            ])["resultat"];
        } catch (NoFFTTResponseException $e) {
            $allParties = [];
        }

        $result = [];
        foreach ($allParties as $partie) {
            if ($partie["victoire"] !== "V" || $partie["forfait"] !== "1") {
                $found = false;
                list($nom, $prenom) = Utils::returnNomPrenom($partie['nom']);
                foreach ($validatedParties as $validatedParty) {
                    if ($partie["date"] === $validatedParty->getDate()->format("d/m/Y")
                        and $nom === $validatedParty->getAdversaireNom()
                        and preg_match('/' . $validatedParty->getAdversairePrenom() . '/', $prenom)
                    ) {
                        $found = true;
                    }
                }

                if (!$found and $prenom != "Absent Absent") {
                    $result[] = new UnvalidatedPartie(
                        $partie["epreuve"],
                        $partie["victoire"] === "V",
                        $partie["forfait"] === "1",
                        \DateTime::createFromFormat('d/m/Y', $partie['date']),
                        $nom,
                        $prenom,
                        Utils::formatPoints($partie["classement"])
                    );
                }
            }
        }
        return $result;
    }

    /**
     * @param string $joueurId
     * @return float points mensuel gagné ou perdu en fonction des points mensuels de l'adverssaire
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     */
    public function getVirtualPoints(string $joueurId): float
    {
        $pointCalculator = new PointCalculator();

        try {
            $classement = $this->getClassementJoueurByLicence($joueurId);

            $unvalidatedParties = $this->getUnvalidatedPartiesJoueurByLicence($joueurId);

            usort($unvalidatedParties, function (UnvalidatedPartie $a, UnvalidatedPartie $b) {
                return $a->getDate() >= $b->getDate();
            }
            );

            $virtualPointWon = 0;
            $latestMonth = null;
            $monthPoints = $classement->getPoints();

            foreach ($unvalidatedParties as $unvalidatedParty) {
                if (!$latestMonth) {
                    $latestMonth = $unvalidatedParty->getDate()->format("m");
                } else {
                    if ($latestMonth != $unvalidatedParty->getDate()->format("m")) {
                        $monthPoints = $classement->getPoints() + $virtualPointWon;
                        $latestMonth = $unvalidatedParty->getDate()->format("m");
                    }
                }

                $coeff = $pointCalculator->getCoefficientOfEpreuve($unvalidatedParty->getEpreuve());
                if (!$unvalidatedParty->isForfait()) {
                    $adversairePoints = $unvalidatedParty->getAdversaireClassement();

                    /**
                     * TODO Refactoring in method
                     */

                    try {
                        $availableJoueurs = $this->getJoueursByNom($unvalidatedParty->getAdversaireNom(), $unvalidatedParty->getAdversairePrenom());
                        foreach ($availableJoueurs as $availableJoueur) {
                            if (floor($unvalidatedParty->getAdversaireClassement() / 100) == $availableJoueur->getPoints()) {
                                $classement = $this->getClassementJoueurByLicence($availableJoueur->getLicence());
                                $adversairePoints = $classement->getPoints();
                                break;
                            }
                        }
                    } catch (NoFFTTResponseException $e) {
                        $adversairePoints = $unvalidatedParty->getAdversaireClassement();
                    }

                    $points = $unvalidatedParty->isVictoire()
                        ? $pointCalculator->getPointVictory($monthPoints, $adversairePoints)
                        : $pointCalculator->getPointDefeat($monthPoints, $adversairePoints);
                    $virtualPointWon += ($points * $coeff);
                }
            }
            return $virtualPointWon;
        } catch (JoueurNotFound $e) {
            return 0;
        }
    }


    /**
     * @param string $clubId
     * @param string|null $type
     * @return Equipe[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getEquipesByClub(string $clubId, string $type = null, $phase = null)
    {
        $params = [
            'numclu' => $clubId,
        ];
        if ($type) {
            $params['type'] = $type;
        }
        
        $result = [];
        
        if ($data = $this->apiRequest->getEquipeByClub('xml_equipe', $params
            )!= null){
                
                $data = $this->apiRequest->getEquipeByClub('xml_equipe', $params
                    );
                
                $data = $this->wrappedArrayIfUnique($data);
                
                $result = [];
                
                //dd($data);
                
                foreach ($data as $dataEquipe) {
                    $lienDivision = "";
                    if ($dataEquipe['liendivision']!=null){
                        $lienDivision = str_replace("&amp;","&",$dataEquipe['liendivision']);
                    }
                    $numPhase = substr($dataEquipe['libequipe'], strlen($dataEquipe['libequipe'])-1);
                    
                    if(null != $phase && $phase == $numPhase){
                        $result[] = new Equipe(
                            $dataEquipe['libequipe'],
                            $dataEquipe['libdivision'],
                            $lienDivision
                            );
                    }
                }
        }
        return $result;
    }

    /**
     * @param string $lienDivision
     * @return EquipePoule[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getClassementPouleByLienDivision(string $lienDivision): array
    {
        $data = $this->apiRequest->getClassementPouleByLienDivision('xml_result_equ', ["action" => "classement"], $lienDivision)['classement'];
        $result = [];
        $lastClassment = 0;
        foreach ($data as $equipePouleData) {

            if (!is_string($equipePouleData['equipe'])) {
                break;
            }
           //dd($equipePouleData);
            $result[] = new EquipePoule(
                $equipePouleData['clt'] === '-' ? $lastClassment : intval($equipePouleData['clt']),
                $equipePouleData['equipe'],
                intval($equipePouleData['joue']),
                intval($equipePouleData['pts']),
                $equipePouleData['numero'],
                intval($equipePouleData['totvic']),
                intval($equipePouleData['totdef']),
                intval($equipePouleData['idequipe']),
                $equipePouleData['idclub'],
                intval($equipePouleData['vic']),
                intval($equipePouleData['def']),
                intval($equipePouleData['nul']),
                intval($equipePouleData['pf'])
            );
            $lastClassment = $equipePouleData['clt'] == "-" ? $lastClassment : intval($equipePouleData['clt']);
        }
        return $result;
    }

    /**
     * @param string $lienDivision
     * @return Rencontre[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getRencontrePouleByLienDivision(string $lienDivision): array
    {
        $data = $this->apiRequest->getRencontrePouleByLienDivision('xml_result_equ', [], $lienDivision);
        $result = [];
        foreach ($data as $dataRencontre) {
            
             $result[] = new Rencontre(
                $dataRencontre['libelle'],
                $dataRencontre['equa'],
                $dataRencontre['equb'],
                intval($dataRencontre['scorea']),
                intval($dataRencontre['scoreb']),
                $dataRencontre['lien'],
                $dataRencontre['dateprevue'],
                $dataRencontre['datereelle']
            );
        }
        return $result;
    }


    /**
     * @param Equipe $equipe
     * @return Rencontre[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getProchainesRencontresEquipe(Equipe $equipe): array
    {
        $nomEquipe = Utils::extractNomEquipe($equipe);
        $rencontres = $this->getRencontrePouleByLienDivision($equipe->getLienDivision());

        $prochainesRencontres = [];
        foreach ($rencontres as $rencontre) {
            if ($rencontre->getDateReelle() === null && $rencontre->getNomEquipeA() === $nomEquipe || $rencontre->getNomEquipeB() === $nomEquipe) {
                $prochainesRencontres[] = $rencontre;
            }
        }
        return $prochainesRencontres;
    }

    /**
     * @param Equipe $equipe
     * @return ClubDetails|null
     * @throws ClubNotFoundException
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getClubEquipe(Equipe $equipe): ?ClubDetails
    {
        $nomEquipe = Utils::extractClub($equipe);
        $club = $this->getClubsByName($nomEquipe);
        if(count($club) === 1){
            return $this->getClubDetails($club[0]->getNumero());
        }

        return null;
    }

    /**
     * @param string $lienRencontre
     * @param string $clubEquipeA
     * @param string $clubEquipeB
     * @return RencontreDetails
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws InvalidLienRencontre
     * @throws NoFFTTResponseException
     */
    public function getDetailsRencontreByLien(string $lienRencontre, string $clubEquipeA = "", string $clubEquipeB = ""): RencontreDetails
    {
        
        //dd($lienRencontre,$clubEquipeA,$clubEquipeB);
        $data = $this->apiRequest->getDetailRencontrByLien('xml_chp_renc', [], $lienRencontre);
        if (!(isset($data['resultat']) && isset($data['joueur']) && isset($data['partie']))) {
            throw new InvalidLienRencontre($lienRencontre);
        }
        $factory = new RencontreDetailsFactory($this);
        //dd($factory->createFromArray($data, $clubEquipeA, $clubEquipeB));
        return $factory->createFromArray($data, $clubEquipeA, $clubEquipeB);
    }
    
    // permet de tester si le resultat journee existe
    public function isDetailsRencontreByLien(string $lienRencontre, string $clubEquipeA = "", string $clubEquipeB = ""): bool
    {
        $data = $this->apiRequest->getDetailRencontrByLien('xml_chp_renc', [], $lienRencontre);
        if (!(isset($data['resultat']) && isset($data['joueur']) && isset($data['partie']))) {
            return false;
        }
        else{
            return true;
        }
        
    }
    
    /**
     * @return Actualite[]
     * @throws Exception\InvalidURIParametersException
     * @throws Exception\URIPartNotValidException
     * @throws NoFFTTResponseException
     */
    public function getActualites(): array
    {
        $data = array();
        if ($this->apiRequest->getActualites('xml_new_actu') != null)
        {
        $data = $this->apiRequest->getActualites('xml_new_actu')['news'];
        $data = $this->wrappedArrayIfUnique($data);
        }
        else{
            return $data;
        }
        $result = [];
       
        foreach ($data as $dataActualite) {
            if(empty($dataActualite['description'])){
                $dataActualite['description'] = $dataActualite['titre'];
            }
            $result[] = new Actualite(
                \DateTime::createFromFormat('Y-m-d', $dataActualite["date"]),
                utf8_decode($dataActualite['titre']),
                $dataActualite['description'],
                $dataActualite['url'],
                $dataActualite['photo'],
                $dataActualite['categorie']
            );
        }
        return $result;
    }


    private function wrappedArrayIfUnique($array): array
    {
        if (count($array) == count($array, COUNT_RECURSIVE)) {
            return [$array];
        }
        return $array;
    }
}
