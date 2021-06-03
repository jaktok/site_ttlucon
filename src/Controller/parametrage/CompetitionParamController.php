<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CompetitionRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Entity\Joueurs;
use App\Repository\JoueursRepository;
use App\Form\JoueurMatchType;
use App\Repository\MatchsRepository;
use App\Entity\Matchs;
use App\Form\MatchsType;
use App\Repository\CategoriesRepository;

class CompetitionParamController extends AbstractController
{
    /**
     * @Route("/dirigeant/param/competition", name="competition_param")
     */
    public function index(Request $request,CompetitionRepository $competRepo, CategoriesRepository $categoriesRepo): Response
    {
        
        // recuperation de la liste des competition
        $listeCompet = $competRepo->findAll();
        
        // recuperation de la liste des Types competition
        $listeCategories = $categoriesRepo->findAll();
        
        foreach ($listeCompet as $compet){
            $competition = new Competition();
            $competition = $compet;
            foreach ($listeCategories as $categorie){
                if ($competition->getCategories() == $categorie){
                    $competition->setCategories($categorie);
                }
            }
        }
        
        $form = $this->createFormBuilder($listeCompet)
        ->getForm();
        return $this->render('parametrage/competition_param/competitions_param.html.twig', [
            'formCompets' =>  $form->createView(),
            'compets'   => $listeCompet
        ]);
    }
    
    /**
     * @Route("/dirigeant/param/competition/nouveau/", name="compet_param_nouveau")
     * @Route("/dirigeant/param/competition/modifier/{id}", name="compet_param_modif")
     *
     */
    public function gerer(Request $request, CompetitionRepository $competRepo, JoueursRepository $joueursRepo, CategoriesRepository $categoriesRepo, int $id = null): Response
    {
        // recuperation de la liste des Types competition
        $listeCategories = $categoriesRepo->findAll();
        
        // recuperation de tous les joueurs tries sur le nom
        $listeJoueurs = $joueursRepo->findBy(array(),array('nom' => 'ASC'));
        
        if ($id){
            // recuperation de l enregistrements selectionne
            $competition = $competRepo->find($id);
            if ($competition) {
                foreach ($listeCategories as $categories){
                    if ($competition->getCategories() == $categories){
                        $competition->setCategories($categories);
                    }
                }
                $form = $this->createForm(CompetitionType::class,$competition);
                $form->handleRequest($request);
            }
        }
        else{
            $competition = new Competition();
            $form = $this->createForm(CompetitionType::class,($competition));
            $form->handleRequest($request);
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($competition);
            $entityManager->flush();
            return $this->redirectToRoute('competition_param');
        }
        
        return $this->render('parametrage/competition_param/fiche_compet_param.html.twig', [
            'formCompet' => $form->createView(),
        ]);
        
    }
    
    /**
     * @Route("/supprime/compet/{id}", name="supprime_compet")
     */
    public function supprimeCompet(Request $request, CompetitionRepository $competRepo, MatchsRepository $matchsRepo, int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $compet = $competRepo->find($id);
        if ($compet) {
            // On supprime d abord les joueurs liés a la compet
            // rechercher les matchs liés à l id competition
            $matchsCompet = $matchsRepo->findByIdCompet($id);
            if ($matchsCompet){
                foreach ($matchsCompet as $match){
                    $entityManager->remove($match);
                    $entityManager->flush();
                }
            }
            // On supprimer la compet
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compet);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('competition_param');
        
    }
    
    /**
     * @Route("/renseigne/compet/{id}", name="renseigne_compet")
     */
    public function renseignerCompet(Request $request, CompetitionRepository $competRepo, MatchsRepository $matchsRepo , JoueursRepository $joueursRepo ,int $id = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        // recuperation de l enregistrements selectionne
        $compet = $competRepo->find($id);
        
        $tabMatchs = array();
        
        if ($compet) {
            $j = 0;
            // rechercher les matchs liés à l id competition
            $matchsCompet = $matchsRepo->findByIdCompet($id);
            $defaultData;
            if ($matchsCompet){
                // recup nbjoueurs
                $tabJoueurs = array();
                $nbJoueur = 0;
                foreach ($matchsCompet as $match){
                    if (!in_array($match->getJoueur()->getId(),$tabJoueurs)){
                        $tabJoueurs[$nbJoueur]= $match->getJoueur()->getId();
                        $nbJoueur++;
                    }
                }
                foreach ($tabJoueurs as $idJoueur){
                    $nbVic = 0;
                    $nbDef = 0;
                    $joueur = $joueursRepo->find($idJoueur);
                    foreach ($matchsCompet as $match){
                        // recuperation de l enregistrements selectionne
                        if($match->getJoueur()->getId()==$idJoueur){
                            
                            if ($match->getVictoire()){
                                $nbVic++;
                            }
                            else{
                                $nbDef++;
                            }
                            $tabMatchs[$j]["position"] =  $match->getPosition();
                        }
                    }
                    $tabMatchs[$j]['nom'] = $joueur->getNom();
                    $tabMatchs[$j]['prenom'] = $joueur->getPrenom();
                    $tabMatchs[$j]["victoires"] =  trim($nbVic);
                    $tabMatchs[$j]["defaites"] =  trim($nbDef);
                    $tabMatchs[$j]["joueur"] =  $joueur;
                    $tabMatchs[$j]['idJoueur'] = $joueur->getId();
                    $tabMatchs[$j]['id'] = $id;
                    $j++;
                    
                    }
            }
          
            $form = $this->createFormBuilder($tabMatchs)
            ->getForm();
        }
        
        return $this->render('parametrage/competition_param/gerer_compet_param.html.twig', [
            'formJoueurMatch' => $form->createView(),
            'tabMatchs'  => $tabMatchs,
            'idCompet' => $id,
            'competition' => $compet
        ]);
        
    }
    
    
    /**
     * @Route("/ajoute/joueurmatch/{idCompet}", name="ajoute_joueurmatch")
     * @Route("/modifie/joueurmatch/{idJoueur}", name="modifie_joueurmatch")
     */
    public function ajouterJoueurMatch(Request $request, CompetitionRepository $competRepo, MatchsRepository $matchsRepo , JoueursRepository $joueursRepo, int $idCompet = null,int $idJoueur = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $tabMatchs = array();
        
        $compet = new Competition();
        if ($idCompet){
            // recuperation de l enregistrements selectionne
            $compet = $competRepo->find($idCompet);
        }
        $joueur = new Joueurs();
        if ($idJoueur){
            $idCompet = $request->query->get('idCompet');
            $compet = $competRepo->find($idCompet);
            $joueur = $joueursRepo->find($idJoueur);
            
            // rechercher les matchs liés à l id competition
            $matchsCompet = $matchsRepo->findByIdCompet($idCompet);
            if ($matchsCompet){
                $nbVic = 0;
                $nbDef = 0;
                    foreach ($matchsCompet as $match){
                        // recuperation de l enregistrements selectionne
                        if($match->getJoueur()->getId()==$idJoueur){
                            
                            if ($match->getVictoire()){
                                $nbVic++;
                            }
                            else{
                                $nbDef++;
                            }
                            $tabMatchs["position"] =  $match->getPosition();
                        }
                    }
                    $tabMatchs["victoires"] =  trim($nbVic);
                    $tabMatchs["defaites"] =  trim($nbDef);
                    $tabMatchs["joueur_compet"] =  $joueur;
            }
          
            $form = $this->createForm(JoueurMatchType::class,array(
                'victoires'  => (trim($nbVic)),
                'defaites'  => (trim($nbDef)),
                'joueur_compet'  => ($joueur),
                'position'  => ($tabMatchs["position"])
            ));
            $form->handleRequest($request);
        }
        else {
            $form = $this->createForm(JoueurMatchType::class);
            $form->handleRequest($request);
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $nbVictoires = (int) $data["victoires"];
            $nbDefaites = (int) $data["defaites"];

            // recup de la lidte des matchs pour suppression
            $player = new Joueurs();
            $player = $data["joueur_compet"];
            $idPlayer = $player->getId();
            $listeMatchs = $matchsRepo->findByIdCompetJoueur($idCompet,$idPlayer);
            foreach ($listeMatchs as $matche){
                $entityManager->remove($matche);
                $entityManager->flush();
            }
            
            for ($i = 0; $i < $nbVictoires;$i++ ){
                $match = new Matchs();
                $match->setVictoire(true);
                $match->setJoueur($data["joueur_compet"]);
                $match->setCompetition($compet);
                $match->setPosition($data["position"]);
                $entityManager->persist($match);
                $entityManager->flush();
            }
            for ($i = 0; $i < $nbDefaites;$i++ ){
                $match = new Matchs();
                $match->setVictoire(false);
                $match->setJoueur($data["joueur_compet"]);
                $match->setCompetition($compet);
                $match->setPosition($data["position"]);
                $entityManager->persist($match);
                $entityManager->flush();
            }
            
          return $this->redirectToRoute('renseigne_compet',array('id' => $compet->getId()));
        }
        return $this->render('parametrage/competition_param/joueurmatch_compet_param.html.twig', [
            'formJoueurMatch' => $form->createView(),
            'idCompet'=> $idCompet
        ]);
        
    }
    
    /**
     * @Route("/supprime/joueurmatch/{idJoueur}", name="supprime_joueurmatch")
     */
    public function supprimeJoueurMatch(Request $request, MatchsRepository $matchsRepo , JoueursRepository $joueursRepo, int $idJoueur = null): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        if ($idJoueur && $request->query->get('idCompet')){
        
        $idCompet = $request->query->get('idCompet');
        // recup de la lidte des matchs pour suppression
        $idPlayer = $request->attributes->get("idJoueur");
        $listeMatchs = $matchsRepo->findByIdCompetJoueur($idCompet,$idPlayer);
        foreach ($listeMatchs as $matche){
            $entityManager->remove($matche);
            $entityManager->flush();
        }
        
            return $this->redirectToRoute('renseigne_compet',array('id' => $idCompet));
        }
        else{
            return $this->redirectToRoute('competition_param');
        }
    }
    
}
