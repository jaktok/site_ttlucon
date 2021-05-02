<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BureauController extends AbstractController
{
    /**
     * @Route("/bureau", name="bureau")
     */
    public function index(): Response
    {
        return $this->render('bureau/bureau.html.twig', [
            'controller_name' => 'BureauController',
        ]);
    }
}
