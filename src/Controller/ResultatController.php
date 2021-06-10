<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RencontresRepository;
use App\Repository\FichiersRepository;

class ResultatController extends AbstractController
{
    /**
     * @Route("/resultat/{cat}", name="resultat")
     */
    public function index(RencontresRepository $rencontreRepo,$cat=null): Response
    {
        switch ($cat) {
            case "Adulte":
                $this->categorie = "Adulte";
                break;
            case "Jeune":
                $this->categorie = "Jeune";
            default :
                $this->categorie = "Adulte";
        }
        return $this->render('resultat/resultat.html.twig', [
            'resultats' => $rencontreRepo->findAll(),
            'categorie' => $this->categorie
        ]);
    }

    /**
     * @Route("/resultat/rencontre/{id}", name="resultat_rencontre")
     */
    public function afficherRencontre(FichiersRepository $fichierRepo,RencontresRepository $rencontreRepo,int $id=null): Response
    {
        if($id){
            $rencontre = $rencontreRepo->find($id);
        }
        $fichier = $rencontre->getFichier();
        //$fichier = $rencontreRepo->findOneByFichier($idFichier);
        //dd($fichier);
        return $this->render('resultat/resultat.html.twig', [
        ]);
    }
}
