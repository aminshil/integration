<?php

// src/Controller/PasswordResetController.php
namespace App\Controller;

use App\Form\PasswordResetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class PasswordResetController extends AbstractController
{
    #[Route('/reinstinaliser-pass', name: 'handle_password_reset')]
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
{
    $user = $this->getUser();

    if (!$user) {
        throw new AccessDeniedException('This action is allowed only for authenticated users.');
    }

    $form = $this->createForm(PasswordResetType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];

        // Check if the old password is correct
        if (!$passwordEncoder->isPasswordValid($user, $oldPassword)) {
            $this->addFlash('error', 'Incorrect old password.');
        } else {
            // Log the current user ID for debugging
            dump('User ID before password change: ' . $user->getId());

            // Encode and set the new password
            $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodedPassword);

            // Log the current user ID after password change for debugging
            dump('User ID after password change: ' . $user->getId());

            // Save the updated user entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Add a flash message
            $this->addFlash('success', 'Le mot de passe a été réinitialisé avec succès. Veuillez vous reconnecter avec le nouveau mot de passe.');

            // Redirect to the home page or any other route you want
            return $this->redirectToRoute('user_interface');
        }
    }

    return $this->render('utilisateur/reintialiser.html.twig', [
        'form' => $form->createView(),
    ]);
}

}