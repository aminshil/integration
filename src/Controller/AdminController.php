<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response {
        $sortField = $request->query->get('sortField', 'id');
        $sortOrder = $request->query->get('sortOrder', 'asc');
        $selectedRole = $request->query->get('selectedRole', null);
        $searchQuery = $request->query->get('searchQuery', null);
    
        $roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_SOCIETE'];
    
        if ($searchQuery) {
            $usersQuery = $userRepository->findBySearchQuery($searchQuery, $sortField, $sortOrder);
        } elseif ($selectedRole && in_array($selectedRole, $roles)) {
            $usersQuery = $userRepository->findByRole($selectedRole, $sortOrder);
        } else {
            $usersQuery = $userRepository->findAllOrderedBy($sortField, $sortOrder);
        }
    
        // Paginate the results
        $users = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page', 1), // Current page number
            10 // Items per page
        );
    
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'selectedRole' => $selectedRole,
            'roles' => $roles,
            'searchQuery' => $searchQuery,
            'user'=>$this->getUser(),
        ]);
    
}
}
