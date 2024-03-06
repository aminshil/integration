<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{


    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $file = $form['image']->getData();

            // Check if a new file is uploaded
            if ($file) {
                $fileName = uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );
                // Update the user's image property
                $user->setImage($fileName);
            } else {
                // If no new file is uploaded, retain the existing image
                $existingImage = $user->getImage();

                // Only update the image property if it was set in the form
                if ($existingImage) {
                    $user->setImage($existingImage);
                }
            }

            // Persist changes to the database
            $entityManager->flush();



        return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/edit', name: 'user_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $file = $form['image']->getData();

            // Check if a new file is uploaded
            if ($file) {
                $fileName = uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );
                // Update the user's image property
                $user->setImage($fileName);
            } else {
                // If no new file is uploaded, retain the existing image
                $existingImage = $user->getImage();

                // Only update the image property if it was set in the form
                if ($existingImage) {
                    $user->setImage($existingImage);
                }
            }

            // Persist changes to the database
            $entityManager->flush();



        return $this->redirectToRoute('user_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/editutilisateur.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/admin/dashboard', name: 'admin_dashboardd')]
    public function dashboard(Request $request, UserRepository $userRepository): Response
    {
        $sortField = $request->query->get('sortField', 'id');  // Default sorting by 'id'
        $sortOrder = $request->query->get('sortOrder', 'asc');

        $users = $userRepository->findAllOrderedBy($sortField, $sortOrder);

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }
    #[Route('/block/{id}', name: 'app_block', methods: ['POST'])]
    public function blockUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Ensure only admins can block users

        $user->setIsBlocked(true);
        $entityManager->flush();

        // Optionally, you can add a flash message or other feedback here

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/unblock/{id}', name: 'app_unblock', methods: ['POST'])]
    public function unblockUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Ensure only admins can unblock users

        $user->setIsBlocked(false);
        $entityManager->flush();

        // Optionally, you can add a flash message or other feedback here

        return $this->redirectToRoute('admin_dashboard');
    }
     
   
    #[Route('/user/stats', name: 'user_stats', methods: ['GET'])]
    public function userStats()
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
    
        $verifiedCount = $userRepository->count(['isVerified' => true]);
        $unverifiedCount = $userRepository->count(['isVerified' => false]);
    
        $data = [
            'verified' => $verifiedCount,
            'unverified' => $unverifiedCount,
        ];
    
        return new JsonResponse($data);
    }
}
