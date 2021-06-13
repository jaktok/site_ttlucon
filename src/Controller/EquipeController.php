<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipeTypeRepository;

use App\Entity\EquipeType;

class EquipeController extends AbstractController
{
    private $categorie;

    /**
     * @Route("/equipe/{cat}", name="equipe")
     */
    public function index(Request $request, EquipeTypeRepository $equipesRepo,$cat=null): Response
    {
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
        }

        $equipes = $equipesRepo->findBy(array(),array('nom' => 'ASC'));
        $telCapitaine = "";
        if($equipes){
            foreach ($equipes as $equipe){
                $telCapitaine = "";
                foreach ($equipe->getJoueur()->getValues() as $joueur){
                    //dd($joueur);
                    if ($joueur->getNom()." ".$joueur->getPrenom() == $equipe->getCapitaine()){
                        $telCapitaine  = $joueur->getTelephone();
                        //dd($telCapitaine);
                    }
                 }
                if($equipe->getSalle()==1){
                    $equipe->setSalle("Salle Jean Jaures");
                }
                else{
                    $equipe->setSalle("Salle Emile Beaussire");
                }
                $equipe->setCapitaine($equipe->getCapitaine()."  -  ".$telCapitaine) ;
              }
           }
        
        //dd($equipes);
        return $this->render('equipe/equipe.html.twig', [
            'equipes' => $equipes,
            'categorie' => $this->categorie,
        ]);
    }
}
