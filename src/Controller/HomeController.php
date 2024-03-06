<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/Affiche', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/AfficheAdmin', name: 'app_admin')]
    public function indexAdmin(): Response
    {
        return $this->render('indexAdmin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


}
