<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\CommentaireType;
use App\Form\CommentaireformationType;
use App\Entity\Commentaire;
use App\Entity\Commentaireformation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentaireRepository;
use App\Repository\CommentaireformationRepository;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }



    #[Route('/supprimercommentaireutilisateur/{id}', name: 'app_supprimer_commentaire_utilisateur')]
    public function supprimercommentaireutilisateur(CommentaireRepository $CommentaireRepository,$id,ManagerRegistry $manager):Response
    {
        $commentaire=new Commentaire();
        $commentaire=$CommentaireRepository->find($id);
        $Evenement = $commentaire->getEvenement();
        $ide=$Evenement->getID();
        $em=$manager->getManager();
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute('app_afficher_evenement_utilisateur', ['id' => $ide]);
    }

    #[Route('/supprimercommentaire/{id}', name: 'app_supprimer_commentaire')]
    public function supprimercommentaire(CommentaireRepository $CommentaireRepository,$id,ManagerRegistry $manager):Response
    {
        $commentaire=new Commentaire();
        $commentaire=$CommentaireRepository->find($id);
        $Evenement = $commentaire->getEvenement();
        $ide=$Evenement->getID();
        $em=$manager->getManager();
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute('app_afficher_evenement', ['id' => $ide]);
    }




    #[Route('/modifiercommentaire/{id}', name: 'app_modifier_commentaire')]
    public function modifiercommentaire(ManagerRegistry $manager,CommentaireRepository $CommentaireRepository,int $id,Request $request): Response
    {
        $commentaire=new Commentaire();
        $commentaire=$CommentaireRepository->find($id);
        $Evenement = $commentaire->getEvenement();
        $form=$this->createForm(CommentaireType::class,$commentaire);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        { 
            $ide=$Evenement->getID();
            $em= $manager->getmanager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('app_afficher_evenement_utilisateur', ['id' => $ide]);
        }
        return $this->render('commentaire/modifiercommentaire.html.twig',
        ['formmodifiercommentaire'=>$form->createView(), 'evenement' => $Evenement,'user'=>$this->getUser()]);
    }











}
