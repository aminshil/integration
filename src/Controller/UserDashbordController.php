<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserDashbordController extends AbstractController
{
    #[Route('/Userr', name: 'user_dashbordd')]
    public function index(): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UserDashbordController',
            'user'=>$this->getUser(),
        ]);
    }

}