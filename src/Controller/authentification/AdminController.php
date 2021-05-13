<?php

namespace App\Controller\authentification;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EditUserType;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('authent/admin/admin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/utilisateurs", name="app_utilisateurs")
     */
    public function usersList(UsersRepository $users)
    {
        return $this->render('authent/admin/users.html.twig', [
            'users' => $users->findAll(),
        ]);
    }

    /**
     * @Route("/admin/utilisateurs/modifier/{id}", name="modifier_utilisateur")
     */
    public function editUser(Users $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_utilisateurs');
        }
        
        return $this->render('authent/admin/edituser.html.twig', [
            'userForme' => $form->createView(),
        ]);
    }


}
