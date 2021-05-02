<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndividuelController extends AbstractController
{
    /**
     * @Route("/individuel", name="individuel")
     */
    public function index(): Response
    {
        return $this->render('individuel/individuel.html.twig', [
            'controller_name' => 'IndividuelController',
        ]);
    }
}
