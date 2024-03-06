<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocieteController extends AbstractController
{
    #[Route('/societe', name: 'societe_dashboard')]
    public function index(): Response
    {
        return $this->render('societe/index.html.twig', [
            'controller_name' => 'SocieteController',
            'user'=>$this->getUser(),
        ]);
    }
}
