<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils , Security $security): Response
    { $user=$this->getUser();
        if ($this->getUser()) {
            if ($user->isBlocked() == 0)
            {
            if ($security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard', ['user' => $user]);}


                else if($security->isGranted('ROLE_USER')) {
                    return $this->redirectToRoute('user_interface', ['user' => $user]);
                
            }else if ($security->isGranted('ROLE_SOCIETE')) {
                return $this->redirectToRoute('societe_dashboard', ['user' => $user]); 
            }  
        } else {
            // Redirect or handle blocked user as needed
            return $this->redirectToRoute('blocked_dashboard');
        }
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
      
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
