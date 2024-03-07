<?php

namespace App\Controller;
use DateTime;
use App\Entity\Depots;
use App\Form\DepotsType;
use App\Repository\DepotsRepository;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Service\SmsGenerator;




class DepotsController extends AbstractController
{
    #[Route('/listDepots', name: 'app_list_Depots')]
public function listDepots(DepotsRepository $DepotsRepository, Request $request): Response
{
    $sortField = $request->query->get('sortField', 'id'); // Default sort field is 'id'
    $sortOrder = $request->query->get('sortOrder', 'asc'); // Default sort order is 'asc'

    $listDepots = $DepotsRepository->findBy([], [$sortField => $sortOrder]);

    return $this->render('Depots/listDepots.html.twig', [
        'listDepots' => $listDepots,
        'sortField' => $sortField,
        'sortOrder' => $sortOrder,
    ]);
}

    #[Route('/listDepotsutilisateur', name: 'app_list_Depots_utilisateur')]
public function listDepotsutilisateur(DepotsRepository $DepotsRepository, Request $request, PaginatorInterface $paginator): Response
{
    // Get all depots from the repository
    $allDepots = $DepotsRepository->findAll();

    // Paginate the depots using the PaginatorInterface
    $listDepots = $paginator->paginate(
        $allDepots, /* query NOT result */
        $request->query->getInt('page', 1)/*page number*/,
        2/*limit per page*/
    );

    // Render the view with the paginated list of depots
    return $this->render('Depots/listDepotsutilisateur.html.twig', [
        'listDepots' => $listDepots,
    ]);
}

#[Route('/pdf', name: 'generate.pdf')]
public function generatePdfPersonne(DepotsRepository $DepotsRepository = null, PdfService $pdf) {
    // Assuming you want to pass the list of depots to the template
    $listDepots = $DepotsRepository->findAll();

    // Pass the correct variable to the template
    $html = $this->render('Depots/pdf_template.html.twig', ['listDepots' => $listDepots]);
    
    $pdf->showPdfFile($html);
}
#[Route('/RecupereDepotsutilisateur', name: 'app_Recupere_Depots_utilisateur')]
    public function RecupereDepotsutilisateur(DepotsRepository $DepotsRepository, FlashyNotifier $flashy, Request $request): Response
    {
        $Depots=new Depots();
        $listDepots = $DepotsRepository->findAll();

        // You can customize the Flashy notification based on your requirements
        $flashy->info('Depot Recuperere! Voir Mail', 'http://your-awesome-link.com');

        return $this->render('Depots/RecupereDepotsutilisateur.html.twig', [
            'listDepots' => $listDepots,
        ]);
    }


    #[Route('/afficherDepots/{id}', name: 'app_afficher_Depots')]
public function afficherDepots(DepotsRepository $DepotsRepository, $id): Response
{
    return $this->render('Depots/afficherDepots.html.twig', [
        'Depots' => $DepotsRepository->find($id),
    ]);
}

#[Route('/afficherDepotsutilisateur/{id}', name: 'app_afficher_Depots_utilisateur')]
public function afficherDepotsutilisateur(DepotsRepository $DepotsRepository, $id): Response
{
    return $this->render('Depots/afficherDepotsutilisateur.html.twig', [
        'Depots' => $DepotsRepository->find($id),
    ]);
}


    #[Route('/ajouterDepots', name: 'app_ajouter_Depots')]
    public function ajouterDepots(ManagerRegistry $manager,Request $request,FlashyNotifier $flashy,SmsGenerator $smsGenerator): Response
    {
        $Depots = new Depots();
        $form= $this->createForm(DepotsType::class,$Depots);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $file = $form->get('Image')->getData();

              if ($file) {
        // Gérez le téléchargement de l'image ici
                   $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                   $file->move(
                   $this->getParameter('uploads_image'), // Chemin vers le dossier d'upload
                   $fileName
        );
        // Stockez le nom du fichier dans l'entité Depots
        $Depots->setImage($fileName);
       }   
            $em=$manager->getManager();
            $em->persist($Depots);
            $em->flush();
            $flashy->success("NEW DEPOT!", 'http://your-awesome-link.com');
            return $this->redirectToRoute('app_list_Depots_utilisateur');
        }
            
            return $this->render('Depots/ajouterDepots.html.twig',[
                'Depots_form' => $form->createView(),
            ]);
    }

    #[Route('/modifierDepots/{id}', name: 'app_modifier_Depots')]
    public function modifierDepots(ManagerRegistry $manager,DepotsRepository $DepotsRepository,int $id,Request $request): Response
    {
        $Depots=new Depots();
        $Depots=$DepotsRepository->find($id);
        $existingImage = $Depots->getImage();
        $form=$this->createForm(DepotsType::class,$Depots);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ 
            $file = $form->get('Image')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('uploads_image'), // Chemin vers le dossier d'upload
                    $fileName
                );
            } 
            else {
                $Depots->setImage($$existingImage);
            }
            $em= $manager->getmanager();
            $em->persist($Depots);
            $em->flush();
            return $this->redirect('/listDepots');
        }
        return $this->render('Depots/modifierDepots.html.twig',
        ['formmodifier'=>$form->createView()]);
    }




    #[Route('/supprimerDepots/{id}', name: 'app_supprimer_Depots')]
    public function supprimerDepots(DepotsRepository $DepotsRepository,$id,ManagerRegistry $manager,FlashyNotifier $flashy):Response
    {
        $Depots=new Depots();
        $Depots=$DepotsRepository->find($id);
        $em=$manager->getManager();
        $em->remove($Depots);
        $em->flush();
        $flashy->error('Depot removed!', 'http://your-awesome-link.com');

        return $this->redirect('/listDepots');
    }


    #[Route('/participate/{id}', name: 'app_participate')]
    public function participate(DepotsRepository $DepotsRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $Depots = $DepotsRepository->find($id);
        $Depots->setNbrparticipants($Depots->getNbrparticipants() + 1);
        $Depots->setParticipating(true);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_list_Depotss_utilisateur');
    }
    
    #[Route('/unparticipate/{id}', name: 'app_unparticipate')]
    public function unparticipate(DepotsRepository $DepotsRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $Depots = $DepotsRepository->find($id);
        $Depots->setNbrparticipants($Depots->getNbrparticipants() - 1);
        $Depots->setParticipating(false);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_list_Depotss_utilisateur');
    }









  
}
