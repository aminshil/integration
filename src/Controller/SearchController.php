<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;  // Ensure this line is present
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class SearchController extends AbstractController
{ 
    #[Route('/search', name: 'app_search')]
        public function search(Request $request, UserRepository $userRepository)
    {
        $criteria = $request->request->all();

        // Use the searchUsers method from UserRepository
        $users = $userRepository->searchUsers($criteria);

        $result = [];

        foreach ($users as $user) {
            // Adapt the structure of the results based on your needs
            $result[] = [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                // Add other fields if needed
            ];
        }

        return new JsonResponse($result);
    }
}

