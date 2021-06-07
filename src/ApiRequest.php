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

const FFTTURL = 'http://www.fftt.com/mobile/pxml/';

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
        $response = $client->request('GET', $uri);
        if($response->getStatusCode() !== 200){
            throw new \DomainException("Request ".$uri." returns an error");
        }
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $response->getBody()->getContents());

        $xml = simplexml_load_string($content);

        return json_decode(json_encode($xml), true);
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
    
    
    // creee pour g�rer le cas de maj auto si on ne trouve pas avec le num�ro de licence 
    // on renvoie null pour gerer le cas plutot qu une exception
    public function getJoueurDetail(string $request, array $params = [], string $queryParameter = null){
        $chaine = $this->prepare($request, $params, $queryParameter);
        try{
            $result =  $this->send($chaine);
        }
        catch (ClientException $ce){
            throw new URIPartNotValidException($request);
        }
        
        if(!$result){
            return null;
         //   throw new InvalidURIParametersException($request, $params);
        }
        if(array_key_exists('0', $result)){
            throw new NoFFTTResponseException($chaine);
        }
        return $result;
    }
}