<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RencontresRepository;
use App\Repository\FichiersRepository;
use App\Repository\EquipeTypeRepository;

class ResultatController extends AbstractController
{
    /**
     * @Route("/resultat/{cat}", name="resultat")
     */
    public function index(RencontresRepository $rencontreRepo,$cat=null,EquipeTypeRepository $equipeRepo): Response
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

        $calendrierPhase1 = $rencontreRepo->findByPhase(1);

        $calendrierPhase2 = $rencontreRepo->findByPhase(2);
        
        return $this->render('resultat/resultat.html.twig', [
            'resultats' => $rencontreRepo->findAll(),
            'categorie' => $this->categorie,
            'calendrierPhase1' => $calendrierPhase1,
            'calendrierPhase2' => $calendrierPhase2,
            'equipes' => $equipeRepo->findBy(array(),array('nom' => 'ASC')),
        ]);
    }

    /**
     * @Route("/resultat/rencontre/{id}", name="resultat_rencontre")
     */
    public function afficherRencontre(KernelInterface $kernelInterface,FichiersRepository $fichierRepo,RencontresRepository $rencontreRepo,int $id=null): Response
    {
        if($id){
            $rencontre = $rencontreRepo->find($id);
        }
        $fichier = $rencontre->getFichier();
        //$fichier = $rencontreRepo->findOneByFichier($idFichier);
        $nom = $fichier->getNom();
        //dd($nom);
        $projetcRoot = $kernelInterface->getProjectDir();
        //dd($projetcRoot);

        //return $this->file($projetcRoot.'/public/resultats/'.$nom);
        //return readfile($projetcRoot.'/public/resultats/'.$nom);
        return new BinaryFileResponse($projetcRoot.'/public/resultats/'.$nom);
    }
}
