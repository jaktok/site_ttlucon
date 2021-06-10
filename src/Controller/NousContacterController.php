<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipeTypeRepository;
use App\Repository\InfosClubRepository;

class NousContacterController extends AbstractController
{
    /**
     * @Route("/nous/contacter", name="nous_contacter")
     */
    public function index(EquipeTypeRepository $equipeRepo, InfosClubRepository $infoRepo): Response
    {
        $info = $infoRepo->findOneByLibelle('mail');
        //dd($info);
        return $this->render('nous_contacter/contact.html.twig', [
            'equipes' => $equipeRepo->findAll(),
            'info' => $info
        ]);
    }
}
