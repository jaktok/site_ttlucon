<?php
/**
 * Created by Antoine Lamirault.
 */

namespace FFTTApi;

use FFTTApi\Exception\InvalidURIParametersException;
use FFTTApi\Exception\NoFFTTResponseException;
use FFTTApi\Exception\URIPartNotValidException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Validator\Constraints\Length;

//const FFTTURL = 'http://www.fftt.com/mobile/pxml/';
const FFTTURL = 'http://apiv2.fftt.com/mobile/pxml/';

class ApiRequest
{
    private $password;
    private $id;

    public function __construct(string $password, string $id)
    {
        $this->password = $password;
        $this->id = $id;
    }

    private function prepare(string $request, array $params = [], string $queryParameter = null) : string{
        $time = round(microtime(true)*1000);
        $timeCrypted = hash_hmac("sha1", $time, $this->password);
        $uri =  FFTTURL.$request.'.php?serie='.$this->id.'&tm='.$time.'&tmc='.$timeCrypted.'&id='.$this->id;
        if($queryParameter){
            $uri.= "&".$queryParameter;
        }
        foreach ($params as $key => $value){
            $uri .= '&'.$key.'='.$value;
        }
        return $uri;
    }

    public function send(string $uri){
        $client = new Client();
        try{
            $response = $client->request('GET', $uri);
        }
        catch (ClientException $ce){
            return null;
        }
        
        if($response->getStatusCode() !== 200){
            throw new \DomainException("Request ".$uri." returns an error");
        }
       
        $contenu = $response->getBody()->getContents();
        $contenu = str_replace("&ugrave;", "ù", $contenu);
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', str_replace("&ecirc;","ê",$contenu));
        $xml = simplexml_load_string($content);
        return json_decode(json_encode($xml), true);
    }
    
    public function sendEquipesByClub(string $uri){
        $client = new Client();
        try{
            $response = $client->request('GET', $uri);
        }
        catch (ClientException $ce){
            return null;
        }
        
        if($response->getStatusCode() !== 200){
            throw new \DomainException("Request ".$uri." returns an error");
        }
        
        $contenu = $response->getBody()->getContents();
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', str_replace("&ecirc;","ê",$contenu));
        $xml = simplexml_load_string($content);
        $tabEquipes = array();
        for ($i = 0; $i < sizeof($xml); $i++) {
            $tabEquipes[$i]["libequipe"] =  (string) $xml->equipe[$i]->libequipe;
            $tabEquipes[$i]["libdivision"] =  (string) $xml->equipe[$i]->libdivision;
            $tabEquipes[$i]["liendivision"] =  (string) $xml->equipe[$i]->liendivision;
            $tabEquipes[$i]["idepr"] =  (string) $xml->equipe[$i]->idepr;
            $tabEquipes[$i]["libepr"] =  (string) $xml->equipe[$i]->libepr;
        }
        
       // return json_decode(json_encode($xml), true);
        return $tabEquipes ;      
    }
    

    
    public function get(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }

        if(!$result){
            throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
    
    public function getRencontrePouleByLienDivision(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->sendRencontrePouleByLienDivision($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            throw new InvalidURIParametersException($request, $params);
        }
       /* if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }*/
        return $result;
    }
    
    
    public function sendRencontrePouleByLienDivision(string $uri){
        $client = new Client();
        try{
            $response = $client->request('GET', $uri);
        }
        catch (ClientException $ce){
            return null;
        }
        
        if($response->getStatusCode() !== 200){
            throw new \DomainException("Request ".$uri." returns an error");
        }
        
        $contenu = $response->getBody()->getContents();
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', str_replace("&ecirc;","ê",$contenu));
        $xml = simplexml_load_string($content);
        $tabRencontres = array();
        for ($i = 0; $i < sizeof($xml); $i++) {
            $tabRencontres[$i]["libelle"] =  (string) $xml->tour[$i]->libelle;
            $tabRencontres[$i]["equa"] =  (string) $xml->tour[$i]->equa;
            $tabRencontres[$i]["equb"] =  (string) $xml->tour[$i]->equb;
            $tabRencontres[$i]["scorea"] =  (string) $xml->tour[$i]->scorea;
            $tabRencontres[$i]["scoreb"] =  (string) $xml->tour[$i]->scoreb;
            $tabRencontres[$i]["lien"] =  (string) $xml->tour[$i]->lien;
            $tabRencontres[$i]["dateprevue"] =  (string) $xml->tour[$i]->dateprevue;
            $tabRencontres[$i]["datereelle"] =  (string) $xml->tour[$i]->datereelle;
        }
        
        // return json_decode(json_encode($xml), true);
        return $tabRencontres ; 
     //   return json_decode(json_encode($xml), true);
    }
    
    public function getActualites(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->sendActualites($chaine);
        }
        catch (\Exception $e){
            return null;
        }
        
        if(!$result){
            return null;
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
    
    public function sendActualites(string $uri){
        $client = new Client();
        try{
            $response = $client->request('GET', $uri);
        }
        catch (ClientException $ce){
            return null;
        }
        
        if($response->getStatusCode() !== 200){
            throw new \DomainException("Request ".$uri." returns an error");
        }
        
        $contenu = $response->getBody()->getContents();
        $contenu = str_replace("&ugrave;", "ù", $contenu);
        $contenu = str_replace("&ocirc;", "ô", $contenu);
        $contenu = str_replace("&acirc;", "â", $contenu);
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', str_replace("&ecirc;","ê",$contenu));
       // $content = preg_replace('/\r|\n/', '', $content);
        $xml = simplexml_load_string($content);
        return json_decode(json_encode($xml), true);
    }
    
    public function getLicencesParClub(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return array_column($result["joueur"], 'licence');;
    }
    
    public function getDetailRencontrByLien(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
    
    
    // cree pour gerer les cas ou la licence n existe pas cote fftt
    public function getPartiesParLicence(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            return null;
            throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
    
    
    // cree pour alleger les stats et ne retourner que les victoires defaites
    public function getPartiesParLicenceStats(string $request, array $params = [], string $queryParameter = null){
        //$params['licence'] = '8525286';
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);if($result){
                $vict = 0;
                $def = 0;
                foreach ($result["partie"] as $resultat){
                    if($resultat["vd"]=="V"){
                        $vict += 1 ;
                    }
                    else{
                        $def += 1;
                    }
                }
                $tabResult = array();
                $tabResult['vict'] = $vict;
                $tabResult['def'] = $def;
            }
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            return null;
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $tabResult;
    }
    
    // cree pour alleger les stats et ne retourner que les victoires defaites avec gestion des parties de la saison en cours
    public function getPartiesParLicenceStatsSaison(string $request, array $params = [],string $annee,string $mois,string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);if($result){
                $vict = 0;
                $def = 0;
                foreach ($result["partie"] as $resultat){
                    if (strlen($resultat["date"])>6){
                        $tabDate = explode("/" ,$resultat["date"]);
                        if (sizeof($tabDate)==3){
                            $moisDate = $tabDate[1];
                            $anDate = $tabDate[2];
                           // dd($moisDate,$anDate,$result);
                            if(($anDate == $annee && $moisDate > $mois) || ($annee > $anDate && $moisDate < $mois) ){
                                if($resultat["vd"]=="V"){
                                    $vict += 1 ;
                                }
                                else{
                                    $def += 1;
                                }
                                }
                            }
                    }
                }
                $tabResult = array();
                $tabResult['vict'] = $vict;
                $tabResult['def'] = $def;
            }
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            return null;
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $tabResult;
    }
    
    // cree pour gerer les cas ou la licence n existe pas cote fftt
    public function getClassementDetail(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            return null;
            throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
    
    
    // creee pour gérer le cas de maj auto si on ne trouve pas avec le numéro de licence 
    // on renvoie null pour gerer le cas plutot qu une exception
    public function getJoueurDetail(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->sendJoueurDetail($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            return null;
        }

        return $result;
    }
    
    public function sendJoueurDetail(string $uri){
        $client = new Client();
        try{
            $response = $client->request('GET', $uri);
        }
        catch (ClientException $ce){
            return null;
        }
        
        if($response->getStatusCode() !== 200){
            throw new \DomainException("Request ".$uri." returns an error");
        }
        
        $contenu = $response->getBody()->getContents();
        $contenu = str_replace("&ugrave;", "ù", $contenu);
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', str_replace("&ecirc;","ê",$contenu));
        
        $xml = simplexml_load_string($content);
        $tabJoueur = array();
            $tabJoueur["idLicence"] =  (string) $xml[0]->licence->idlicence;
            $tabJoueur["licence"] =  (string) $xml[0]->licence->licence;
            $tabJoueur["nom"] =  (string) $xml[0]->licence->nom;
            $tabJoueur["prenom"] =  (string) $xml[0]->licence->prenom;
            $tabJoueur["numclub"] =  (string) $xml[0]->licence->numclub;
            $tabJoueur["nomclub"] =  (string) $xml[0]->licence->nomclub;
            $tabJoueur["sexe"] =  (string) $xml[0]->licence->sexe;
            $tabJoueur["type"] =  (string) $xml[0]->licence->type;
            $tabJoueur["certif"] =  (string) $xml[0]->licence->certif;
            $tabJoueur["validation"] =  (string) $xml[0]->licence->validation;
            $tabJoueur["echelon"] =  (string) $xml[0]->licence->echelon;
            $tabJoueur["place"] =  (string) $xml[0]->licence->place;
            $tabJoueur["point"] =  (string) $xml[0]->licence->point;
            $tabJoueur["cat"] =  (string) $xml[0]->licence->cat;
            $tabJoueur["pointm"] =  (string) $xml[0]->licence->pointm;
            $tabJoueur["apointm"] =  (string) $xml[0]->licence->apointm;
            $tabJoueur["initm"] =  (string) $xml[0]->licence->initm;
            $tabJoueur["mutation"] =  (string) $xml[0]->licence->mutation;
            $tabJoueur["natio"] =  (string) $xml[0]->licence->natio;
            $tabJoueur["arb"] =  (string) $xml[0]->licence->arb;
            $tabJoueur["ja"] =  (string) $xml[0]->licence->ja;
            $tabJoueur["tech"] =  (string) $xml[0]->licence->tech;
        
            return $tabJoueur ;
    }
    
    
    
    
    public function getEquipeByClub(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->sendEquipesByClub($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
        return null;    
        throw new InvalidURIParametersException($request, $params);
        }
        /*if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }*/
        return $result;
    }
    
    
    public function getUnvalidatedPartiesJoueurByLicence(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
    
    
}