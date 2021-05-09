<?php

namespace App\Controller\parametrage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexParamController extends AbstractController
{
    /**
     * @Route("/param/index/", name="index_param")
     */
    public function index(): Response
    {
        return $this->render('parametrage/index_parametrage/index_parametrage.html.twig', [
            'controller_name' => 'IndexParamController',
        ]);
    }
}
