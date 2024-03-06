<?php

namespace App\Controller;

use App\Entity\Commentaireformation;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Form\CommentaireformationType;
use App\Repository\FormationRepository;
use App\Repository\CommentaireformationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use ContainerRa4Bhng\getBadWordFilterService;
use App\Service\BadWordFilter;
use Endroid\QrCode\QrCode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Endroid\QrCodeBundle;
class FormationController extends AbstractController
{#[Route('/formation', name: 'app_formation_index', methods: ['GET'])]
public function index(Request $request, FormationRepository $formationRepository, PaginatorInterface $paginator): Response
{

    // Récupérer les paramètres de tri depuis la requête
    $sortBy = $request->query->get('sort_by', 'nom_formation'); // Tri par défaut par nom
    $sortOrder = $request->query->get('sort_order', 'asc'); // Ordre de tri par défaut
    $query = $formationRepository->findAllSortedByIdQuery($sortOrder);
    switch ($sortBy) {
        case 'nom_formation':
            $query = $formationRepository->findAllSortedByNomQuery($sortOrder);
            break;
        case 'date_formation':
            $query = $formationRepository->findAllSortedByDateQuery($sortOrder);
            break;
        case 'id':
            $query = $formationRepository->findAllSortedByIdQuery($sortOrder);
            break;
    }
    // Récupérer le paramètre de recherche depuis la requête
    $searchName = $request->query->get('search_name');

    // Appliquer le filtre de recherche si fourni
    if ($searchName) {
        $query = $formationRepository->findByNomQuery($searchName);
    }

    // Paginer les résultats
    $formations = $formationRepository->paginateQuery($query, $request->query->getInt('page', 1), 3);

    return $this->render('formation/index.html.twig', [
        'formations' => $formations,'user'=>$this->getUser()
    ]);
}
    #[Route('/formation/admin', name: 'app_formation_indexadmin', methods: ['GET'])]
    public function indexadmin(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/indexadmin.html.twig', [
            'formations' => $formationRepository->findAll(),'user'=>$this->getUser()
        ]);
    }

    #[Route('/formation/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request,FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
        $formation = new Formation();
        $formation->setFormationuser($user);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image_formationFile = $form->get('image_formation')->getData();
            if ($image_formationFile) {
                $image_formationFileName = $fileUploader->upload($image_formationFile);
                $formation->setImageFormation($image_formationFileName);}

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_indexadmin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,'user'=>$this->getUser()
        ]);
    }

    #[Route('/formation/{id}', name: 'app_formation_show', methods: ['GET','POST'])]
    public function show(Formation $formation,Request $request, EntityManagerInterface $entityManager,BadWordFilter $badWordFilter): Response
    {
        $user=$this->getUser();
        $commentaire = new Commentaireformation();
        $commentaire ->setDateCreation(new DateTimeImmutable());
        $commentaire ->setFormation($formation); 
        $commentaire ->setCommentaireuserformation($user);
        $form = $this->createForm(CommentaireformationType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire ->setDateCreation(new DateTimeImmutable());
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_show', [
                'id'=> $formation->getId()
            ], Response::HTTP_SEE_OTHER);
        }


        $qrCodeData = [
            'nomFormation' => $formation->getNomFormation(),
            'formateur' => $formation->getFormateur(),
            'dateFormation' => $formation->getDateFormation()->format('Y-m-d'),
            'lieuFormation' => $formation->getLieuFormation(),
            'discription' => $formation->getDescriptionFormation(),
        ];

        // Convertir les données en JSON
        $jsonQrCodeData = json_encode($qrCodeData);

        // Générer le QR code
        $qrCode = new QrCode($jsonQrCodeData);
        $qrCodePath = 'uploads/qrcodes/formation_' . $formation->getId() . '.png';
        try {
            $qrCode->writeFile($qrCodePath);
        } catch (\Exception $e) {
            echo 'Failed to save QR code image: ' . $e->getMessage();}
            $commentaire = $formation->getCommentaireformations();
        return $this->renderForm('formation/show.html.twig', [
            'formation' => $formation,
            'commentaire' => $commentaire,
            'form' => $form,
            'badWordFilter' => $badWordFilter,
            'qrCodePath' => $qrCodePath,'user'=>$this->getUser()
        ]);
    }


    #[Route('/formation/admin/{id}', name: 'app_formation_showadmin', methods: ['GET'])]
    public function showadmin(Formation $formation): Response
    {
        return $this->render('formation/showadmin.html.twig', [
            'formation' => $formation,'user'=>$this->getUser()
        ]);
    }

    #[Route('/formation/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation,FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image_formationFile = $form->get('image_formation')->getData();
            if ($image_formationFile) {
                $image_formationFileName = $fileUploader->upload($image_formationFile);
                $formation->setImageFormation($image_formationFileName);}
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_indexadmin', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,'user'=>$this->getUser()
        ]);
    }

    #[Route('/formation/delete/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_indexadmin', [], Response::HTTP_SEE_OTHER);
    }








#[Route('/supprimercommentairformation/{id}', name: 'app_supprimer_commentaire_formation')]
public function supprimercommentairformation(CommentaireformationRepository $CommentaireRepository,$id,ManagerRegistry $manager):Response
{
    $commentaire=new Commentaireformation();
    $commentaire=$CommentaireRepository->find($id);
    $formation = $commentaire->getFormation();
    $ide=$formation->getID();
    $em=$manager->getManager();
    $em->remove($commentaire);
    $em->flush();
    return $this->redirectToRoute('app_formation_show', ['id' => $ide]);
}
    



#[Route('/modifiercommentaireformation/{id}', name: 'app_modifier_formation_commentaire')]
public function modifiercommentaireformation(ManagerRegistry $manager,CommentaireformationRepository $CommentaireRepository,int $id,Request $request): Response
{
    $commentaire=new Commentaireformation();
    $commentaire=$CommentaireRepository->find($id);
    $formation = $commentaire->getFormation();
    $form=$this->createForm(CommentaireformationType::class,$commentaire);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid())
    { 
        $ide=$formation->getID();
        $em= $manager->getmanager();
        $em->persist($commentaire);
        $em->flush();
        return $this->redirectToRoute('app_formation_show', ['id' => $ide]);
    }
    return $this->render('commentaire/edit.html.twig',
    ['form'=>$form->createView(), 'formation' => $formation,'user'=>$this->getUser()]);
}





#[Route('/formation/{id}/participate', name:"formation_participate", methods: ['POST'])]
public function participateformation(Request $request, Formation $formation): Response
{
   $user = $this->getUser(); // Assuming you are using Symfony's security component

   // Add the user to the participants of the event
   $formation->addParticipation($user);

   $entityManager = $this->getDoctrine()->getManager();
   $entityManager->persist($formation);
   $entityManager->flush();

   // Redirect to the event page or wherever you want
   return $this->redirectToRoute('app_formation_show', ['id' => $formation->getId()]);
}



#[Route('/formation/{id}/unparticipate', name:"formation_unparticipate", methods: ['POST'])]
public function unparticipateformation(Request $request, Formation $formation): Response
{
   $user = $this->getUser(); // Assuming you are using Symfony's security component

   // Remove the user from the participants of the event
   $formation->removeParticipation($user);
   $entityManager = $this->getDoctrine()->getManager();
   $entityManager->persist($formation);
   $entityManager->flush();

   // Redirect to the event page or wherever you want
   return $this->redirectToRoute('app_formation_show', ['id' => $formation->getId()]);
}




}

