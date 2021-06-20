<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JoueursRepository;
use App\Repository\MatchsRepository;
use App\Repository\RencontresRepository;

class StatistiquesDoubleController extends AbstractController
{
    /**
     * @Route("/statistiques/double/{cat}", name="statistiques_double")
     */
    public function index(JoueursRepository $joueursRepo,RencontresRepository $rencontreRepo, MatchsRepository $matchsRepo, $cat=null): Response
    {
        
        $tabDoubles = $matchsRepo->findDoubles();
        
        $tabDoublesStat = array();
        $tabIdDoubles = array();
        $cpt = 0;
        foreach ($tabDoubles as $double){
            $categorie = "Adulte";
            if ($double->getRencontre()){
            $idRencontre = $double->getRencontre()->getId();  
            $rencontre = $rencontreRepo->findBy(['id'=>$idRencontre]);
            if($rencontre[0]->getScoreA() != null && $rencontre[0]->getScoreB() != null ){
                if($rencontre[0]->getScoreA()+$rencontre[0]->getScoreB()<=10){
                    $categorie  = "Jeune";
                }
            }
            $idDouble = "";
            if($double->getIdJoueur1() > $double->getIdJoueur2() ){
                $idDouble = $double->getIdJoueur2()."_".$double->getIdJoueur1();
                $joueur1 = $joueursRepo->find($double->getIdJoueur2());
                $joueur2 = $joueursRepo->find($double->getIdJoueur1());
            }
            else{
                $idDouble = $double->getIdJoueur1()."_".$double->getIdJoueur2();
                $joueur1 = $joueursRepo->find($double->getIdJoueur1());
                $joueur2 = $joueursRepo->find($double->getIdJoueur2());
            }
            $victoire = 0;
            $defaite = 0;
            if ($double->getVictoire()){
                $victoire = 1;
            }
            else{
                $defaite = 1;
            }
            
            if ($categorie == $cat){
                if (!in_array($idDouble, $tabIdDoubles) ){
                    $tabDoublesStat[$cpt]["joueur1"]= $joueur1 ;
                    $tabDoublesStat[$cpt]["joueur2"]= $joueur2 ;
                    $tabDoublesStat[$cpt]["id_double"]= $idDouble ;
                    $tabDoublesStat[$cpt]["victoires"]= $victoire ;
                    $tabDoublesStat[$cpt]["defaites"]= $defaite ;
                    $tabDoublesStat[$cpt]["categorie"]= $categorie ;
                    array_push($tabIdDoubles,$idDouble);
                    $cpt++;
                }
                else {
                    foreach ($tabDoublesStat as $key => $val) {
                        if ($val['id_double'] === $idDouble) {
                            if($victoire==1){
                                $tabDoublesStat[$key]["victoires"] += $victoire ;
                            }
                            else{
                                $tabDoublesStat[$key]["defaites"] += $defaite ;
                            }
                            break;
                        }
                    }
                }
            }
          }// fin if double->getRencontre
        }
        for($j=0;$j < sizeof($tabDoublesStat);$j++){
            $pourcent = ($tabDoublesStat[$j]['victoires']*100)/($tabDoublesStat[$j]['victoires']+$tabDoublesStat[$j]['defaites']);
            $pourcent = round($pourcent,0);
            $tabDoublesStat[$j]['pourcent'] = $pourcent;
        }
        $colPourcent = array_column($tabDoublesStat, 'pourcent');
        $colVicoires = array_column($tabDoublesStat, 'victoires');
        array_multisort($colPourcent, SORT_DESC,$colVicoires,SORT_DESC,  $tabDoublesStat);
        
        $tabJoueurs = array();
        foreach ($tabDoublesStat as $stat){
            array_push($tabJoueurs,$stat["joueur1"]);
            array_push($tabJoueurs,$stat["joueur2"]);
        }
        $tabJoueurs = array_unique($tabJoueurs,SORT_REGULAR  );
        
        $tabJoueurDouble = array();
        $tabIdJoueurs = array();
        $j=0;
        $i=0;
        if($tabDoublesStat){
            if($tabDoublesStat[0]["categorie"]==$cat){
                //dd($tabJoueurs);
                foreach($tabJoueurs as $joueur){
                    if (!in_array($joueur->getId(), $tabIdJoueurs)){
                        $tabIdJoueurs[$j]= $joueur->getId();
                        $j++;
                    
                        $tabJoueurDouble[$i]["joueur"]= $joueur;
                        $tabJoueurDouble[$i]["categorie"]= $cat;
                        $tabJoueurDouble[$i]["victoires"]= 0;
                        $tabJoueurDouble[$i]["defaites"]= 0;
                        foreach ($tabDoublesStat as $stat){
                            if($stat["joueur1"]->getId() == $joueur->getId() || $stat["joueur2"]->getId() == $joueur->getId()){
                                $tabJoueurDouble[$i]["victoires"]+= $stat["victoires"];
                                $tabJoueurDouble[$i]["defaites"]+= $stat["defaites"];
                            }
                        }
                        $pourcent = ($tabJoueurDouble[$i]['victoires']*100)/($tabJoueurDouble[$i]['victoires']+$tabJoueurDouble[$i]['defaites']);
                        $pourcent = round($pourcent,0);
                        $tabJoueurDouble[$i]['pourcent'] = $pourcent;
                        $i++;
                        if (!in_array($joueur->getId(), $tabIdJoueurs)){
                            $tabIdJoueurs[$j]= $joueur->getId();
                            $j++;
                        }
                    }
                }
            }
        }
        $colPourcentJoueur = array_column($tabJoueurDouble, 'pourcent');
        $colVictoiresJoueur = array_column($tabJoueurDouble, 'victoires');
        array_multisort($colPourcentJoueur, SORT_DESC,$colVictoiresJoueur,SORT_DESC, $tabJoueurDouble);
        $pos = 1;
        for($i=0; $i < sizeof($tabJoueurDouble); $i++){
            if($i==0){
                $tabJoueurDouble[$i]['position'] = $pos;
                $pos++;
            }
            if ($i!=0 && $tabJoueurDouble[$i]['pourcent'] ==  $tabJoueurDouble[$i-1]['pourcent']){
                $tabJoueurDouble[$i]['position'] = $tabJoueurDouble[$i-1]['position'];
            }
            else if ($i!=0 && $tabJoueurDouble[$i]['pourcent'] !=  $tabJoueurDouble[$i-1]['pourcent']){
                $tabJoueurDouble[$i]['position'] = $pos;
                $pos++;
            }
            
        }
        
        $pos = 1;
        for($i=0; $i < sizeof($tabDoublesStat); $i++){
            if($i==0){
                $tabDoublesStat[$i]['position'] = $pos;
                $pos++;
            }
            if ($i!=0 && $tabDoublesStat[$i]['pourcent'] ==  $tabDoublesStat[$i-1]['pourcent']){
                $tabDoublesStat[$i]['position'] = $tabDoublesStat[$i-1]['position'];
            }
            else if ($i!=0 && $tabDoublesStat[$i]['pourcent'] !=  $tabDoublesStat[$i-1]['pourcent']){
                $tabDoublesStat[$i]['position'] = $pos;
                $pos++;
            }
            
        }
        
       //dd($tabDoublesStat,$tabIdDoubles,$tabJoueurDouble);
        
        return $this->render('statistiques_double/statistiques_double.html.twig', [
            'tabDoubleStats' => $tabDoublesStat,
            'tabJoueurDouble' => $tabJoueurDouble,
            'categorie' => $cat,
        ]);
    }
}
