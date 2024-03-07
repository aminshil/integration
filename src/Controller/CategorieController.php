<?php

namespace App\Controller;
use DateTime;
use App\Entity\CategorieDepot;
use App\Form\CategorieDepotType;
use App\Repository\CategorieDepotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;



class CategorieController extends AbstractController
{
    #[Route('/listCategorie', name: 'app_list_Categorie')]
    public function listCategorie(CategorieDepotRepository $CategorieRepository, Request $request ): Response
    {
        $sortField = $request->query->get('sortField', 'id'); // Default sort field is 'id'
        $sortOrder = $request->query->get('sortOrder', 'asc'); // Default sort order is 'asc'
        $listCategorie = $CategorieRepository->findBy([], [$sortField => $sortOrder]);

        return $this->render('CategorieDepot/listCategorie.html.twig', [

        'listCategorie' => $listCategorie,
        'sortField' => $sortField,
        'sortOrder' => $sortOrder,
    ]);
    
    }

    #[Route('/listCategorieutilisateur', name: 'app_list_Categorie_utilisateur')]
    public function listCategorieutilisateur(CategorieDepotRepository $CategorieRepository): Response
    {
        return $this->render('CategorieDepot/listCategorieutilisateur.html.twig', [
            'listCategorie' => $CategorieRepository->findall(),
        ]);
    }


    #[Route('/afficherCategorie/{id}', name: 'app_afficher_Categorie')]
public function afficherCategorie(CategorieDepotRepository $CategorieRepository, $id): Response
{
    return $this->render('Categorie/afficherCategorie.html.twig', [
        'Categorie' => $CategorieRepository->find($id),
    ]);
}

#[Route('/afficherCategorieutilisateur/{id}', name: 'app_afficher_Categorie_utilisateur')]
public function afficherCategorieutilisateur(CategorieDepotRepository $CategorieRepository, $id): Response
{
    return $this->render('Categorie/afficherCategorieutilisateur.html.twig', [
        'Categorie' => $CategorieRepository->find($id),
    ]);
}



    #[Route('/ajouterCategorie', name: 'app_ajouter_Categorie')]
    public function ajouterCategorie(ManagerRegistry $manager,Request $request): Response
    {
        $Categorie = new CategorieDepot();
        $form= $this->createForm(CategorieDepotType::class,$Categorie);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em=$manager->getManager();
            $em->persist($Categorie);
            $em->flush();
            return $this->redirect('/listCategorie');
            }
            return $this->render('CategorieDepot/ajouterCategorie.html.twig',[
                'Categorie_form' => $form->createView(),
            ]);
    }

    #[Route('/modifierCategorie/{id}', name: 'app_modifier_Categorie')]
    public function modifierCategorie(ManagerRegistry $manager, CategorieDepotRepository $CategorieRepository, int $id, Request $request): Response
    {
        $Categorie=new CategorieDepot();
        $Categorie=$CategorieRepository->find($id);
        $form=$this->createForm(CategorieDepotType::class,$Categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        { 
           $em= $manager->getmanager();
            $em->persist($Categorie);
            $em->flush();
            return $this->redirect('/listCategorie');
        }
        return $this->render('CategorieDepot/modifierCategorie.html.twig',
        ['formmodifier'=>$form->createView()]);
    }




    #[Route('/supprimerCategorie/{id}', name: 'app_supprimer_Categorie')]
    public function supprimerCategorie(CategorieDepotRepository $CategorieRepository, $id, ManagerRegistry $manager):Response
    {
        $Categorie=new CategorieDepot();
        $Categorie=$CategorieRepository->find($id);
        foreach ($Categorie->getDepot() as $depot) {
        $depot->setCategorie(null);}
        $em=$manager->getManager();
        $em->remove($Categorie);
        $em->flush();
        return $this->redirect('/listCategorie');
    }


    #[Route('/participate/{id}', name: 'app_participate')]
    public function participate(CategorieDepotRepository $CategorieRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $Categorie = $CategorieRepository->find($id);
        $Categorie->setNbrparticipants($Categorie->getNbrparticipants() + 1);
        $Categorie->setParticipating(true);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_list_Categories_utilisateur');
    }
    
    #[Route('/unparticipate/{id}', name: 'app_unparticipate')]
    public function unparticipate(CategorieDepotRepository $CategorieRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $Categorie = $CategorieRepository->find($id);
        $Categorie->setNbrparticipants($Categorie->getNbrparticipants() - 1);
        $Categorie->setParticipating(false);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_list_Categories_utilisateur');
    }


    #[Route('/getDepotsByCategorie/{CategorieId}', name: 'get_depots_by_Categorie')]
public function getDepotsByCategorie(DepotRepository $depotRepository, $CategorieId): JsonResponse
{
    // Implement your logic to retrieve depots based on the  Categorie ID
    $depots = $depotRepository->findBy([' Categorie' => $CategorieId]);

    // Convert depots to an array or any format suitable for JSON response
    $depotsArray = [];
    foreach ($depots as $depot) {
        $depotsArray[] = [
            'Nom' => $depot->getNom(), // Adjust this based on your depot entity
            // Add other depot properties as needed
        ];
    }

    return new JsonResponse($depotsArray);
}







  
}
