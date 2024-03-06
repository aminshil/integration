<?php

namespace App\Controller;
use DateTime;
use App\Entity\Evenement;
use App\Entity\Commentaire;
use App\Form\EvenementType;
use App\Form\CommentaireType;
use App\Repository\EvenementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BadWordFilterService;
use App\Service\FileUploader;
use App\Service\MyGmailMailerService;
use App\Service\SmsGenerator;
use App\Service\EvenementStateService;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\NotBlank;

class EvenementController extends AbstractController
{

#for mail :
    private MyGmailMailerService $mailerService;

    public function __construct(MyGmailMailerService $mailerService)
    {
        $this->mailerService = $mailerService;

    }


    

    #[Route('/listevenements', name: 'app_list_evenements')]
    public function listevenements(Request $request,EvenementRepository $EvenementRepository,EvenementStateService $evenementStateService): Response
    {
        
        $evenementStateService->updateAllEvenementsState();
        $sortBy = $request->query->get('sort_by', 'nom');
        $sortOrder = $request->query->get('sort_order', 'asc');
        
        $sortedEvenements = $EvenementRepository->findAllSortedBy($sortBy, $sortOrder);
        
        return $this->render('evenement/listevenements.html.twig', [
            'listevenements' => $sortedEvenements,'sort_order' => $sortOrder,'user'=>$this->getUser(),
        ]);
    }


    

    #[Route('/listevenementsutilisateur', name: 'app_list_evenements_utilisateur')]
public function listevenementsutilisateur(Request $request, EvenementRepository $EvenementRepository,EvenementStateService $evenementStateService): Response
{
    $evenementStateService->updateAllEvenementsState();
    $sortBy = $request->query->get('sort_by', 'nom');
    $sortOrder = $request->query->get('sort_order', 'asc');
    
    $sortedEvenements = $EvenementRepository->findAllSortedBy($sortBy, $sortOrder);
    
    return $this->render('evenement/listevenementsutilisateur.html.twig', [
        'listevenements' => $sortedEvenements,'sort_order' => $sortOrder,'user'=>$this->getUser()
    ]);
}






    #[Route('/listevenementsfinisutilisateur', name: 'app_list_evenements_finis_utilisateur')]
    public function listevenementsfinisutilisateur(Request $request,EvenementRepository $EvenementRepository): Response
    {

        $sortBy = $request->query->get('sort_by', 'nom');
        $sortOrder = $request->query->get('sort_order', 'asc');
        
        $sortedEvenements = $EvenementRepository->findAllSortedBy($sortBy, $sortOrder);
        return $this->render('evenement/listevenementsfinisutilisateur.html.twig', [
            'listevenements' => $sortedEvenements,'sort_order' => $sortOrder,'user'=>$this->getUser()
        ]);
    }

    




    #[Route('/afficherevenement/{id}', name: 'app_afficher_evenement')]
public function afficherevenement(EvenementRepository $EvenementRepository, $id): Response
{
    return $this->render('evenement/afficherevenement.html.twig', [
        'evenement' => $EvenementRepository->find($id),'user'=>$this->getUser()
    ]);
}



#[Route('/afficherevenementutilisateur/{id}', name: 'app_afficher_evenement_utilisateur')]
public function afficherevenementutilisateur(Request $request,EvenementRepository $EvenementRepository, $id,EntityManagerInterface $em,BadWordFilterService $badWordFilterService): Response
{
    $user=$this->getUser();
    $evenement = new Evenement();
    $evenement = $EvenementRepository->find($id);
    $commentaire = new Commentaire();
    $commentaire->setDatecreation(new \DateTime());
    $commentaire->setEvenement($evenement);
    $commentaire->setCommentaireuser($user);
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $commentaire->setDatecreation(new \DateTime());
        $commentaire->setContenu($badWordFilterService->filter($commentaire->getContenu()));
        $em->persist($commentaire);
        $em->flush();
        return $this->redirectToRoute('app_afficher_evenement_utilisateur', [
            'id' => $evenement->getId(),'user'=>$this->getUser()
        ], Response::HTTP_SEE_OTHER);
    }
    $commentaires = $evenement->getCommentaires();
    return $this->renderForm('evenement/afficherevenementutilisateur.html.twig',  [
        'evenement' => $evenement,
        'commentaires' => $commentaires,
        'commentaire_form' => $form
        ,'user'=>$this->getUser()
    ]);


}




#[Route('/ajouterevenement', name: 'app_ajouter_evenement')]
    public function ajouterevenement(ManagerRegistry $manager,Request $request,FileUploader $fileUploader,BadWordFilterService $badWordFilterService,SmsGenerator $SmsGenerator,UserRepository $userrepository): Response
    {
        $user=$this->getUser();
        $evenement = new Evenement();
        $evenement->setUserEvenement($user);
        $currentDate=new \DateTime();
        $currentDate->setTime($currentDate->format('H'), $currentDate->format('i'), 0);
        $evenement->setEtat("à venir");
        $evenement->setDateDebut( $currentDate);
        $evenement->setDateFin( $currentDate);
        $form= $this->createForm(EvenementType::class,$evenement);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){

//image
$imageFile = $form->get('image')->getData();
if ($imageFile) {
    $imageFileName = $fileUploader->upload($imageFile);
    $evenement->setImage($imageFileName);}

            //map
            $latitude = $form->get('latitude')->getData();
            $longitude = $form->get('longitude')->getData();
            $location = sprintf('%f,%f', $latitude, $longitude);
            $evenement->setLocation($location);


                $evenement->setDescription($badWordFilterService->filter($evenement->getDescription()));
                $evenement->setObjectif($badWordFilterService->filter($evenement->getObjectif()));
                $evenement->setNom($badWordFilterService->filter($evenement->getNom()));
            $em=$manager->getManager();
            $em->persist($evenement);
            $em->flush();

//mail

$users = $userrepository->findAll();
foreach ($users as $user) {
            $this->mailerService->sendEmail(
                $user->getEmail(),
                'Nouveau evenement',
                $this->renderView('email/email_template.html.twig', [
                    'evenement' => $evenement,
                ])
            );
        }

//sms

$formattedDateDebut = $evenement->getDateDebut() ? $evenement->getDateDebut()->format('Y-m-d H:i') : ''; // Convert DateTime to string using format() method
$number_test='+21629131606';
$name='EcoPartage';
$text = "Un nouveau évènement a été créé :". $evenement->getNom()." pour ".$evenement->calculatePeriod()." la date de début est ".$formattedDateDebut. " dans ".$evenement->getLocationtext()."visitez notre site pour plus d'informatio bonne journée";
$SmsGenerator->sendSms($number_test ,$name,$text);
            

            return $this->redirect('/listevenements');
            }
            return $this->render('evenement/ajouterevenement.html.twig',[
                'evenement_form' => $form->createView(),'user'=>$this->getUser(),
            ]);
    }





    #[Route('/modifierevenement/{id}', name: 'app_modifier_evenement')]
    public function modifierevenement(ManagerRegistry $manager,EvenementRepository $EvenementRepository,int $id,Request $request,FileUploader $fileUploader): Response
    {
        
        $evenement=new Evenement();
        $evenement=$EvenementRepository->find($id);
        $form=$this->createForm(EvenementType::class,$evenement);
        $form->handleRequest($request);
        //remove error for notblank with errormessage:
        $imageErrors = $form->get('image')->getErrors();
        // Iterate over errors and remove the "NotBlank" error if it exists
        foreach ($imageErrors as $error) {
            if ($error->getMessage() === 'Veuillez sélectionner une image.') {
                $form->get('image')->clearErrors();
                break; // Exit loop after clearing the error
            }
        }
        
        if($form->isSubmitted() && $form->isValid())
        { 
            //image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $evenement->setImage($imageFileName);}

           $em= $manager->getmanager();
            $em->persist($evenement);
            $em->flush();
            return $this->redirect('/listevenements');
        }
        return $this->render('evenement/modifierevenement.html.twig',
        ['formmodifier'=>$form->createView(),'user'=>$this->getUser()]);
    }




    #[Route('/supprimerevenement/{id}', name: 'app_supprimer_evenement')]
    public function supprimerevenement(EvenementRepository $EvenementRepository,$id,ManagerRegistry $manager):Response
    {
        $evenement=new Evenement();
        $evenement=$EvenementRepository->find($id);
        if ($evenement->getEtat() == 'supprimé') {
            $em = $manager->getManager();
            $em->remove($evenement);
            $em->flush();
        } else {
            $evenement->setEtat('supprimé');
            $em = $manager->getManager();
            $em->persist($evenement);
            $em->flush();
        }
        return $this->redirect('/listevenements');
    }


    
#[Route('/evenement/{id}/participate', name:"evenement_participate", methods: ['POST'])]
public function participate(Request $request, Evenement $evenement): Response
{
   $user = $this->getUser(); // Assuming you are using Symfony's security component

   // Add the user to the participants of the event
   $evenement->addParticipationevenement($user);

   $entityManager = $this->getDoctrine()->getManager();
   $entityManager->persist($evenement);
   $entityManager->flush();

   // Redirect to the event page or wherever you want
   return $this->redirectToRoute('app_list_evenements_utilisateur', ['id' => $evenement->getId()]);
}



#[Route('/evenement/{id}/unparticipate', name:"evenement_unparticipate", methods: ['POST'])]
public function unparticipate(Request $request, Evenement $evenement): Response
{
   $user = $this->getUser(); // Assuming you are using Symfony's security component

   // Remove the user from the participants of the event
   $evenement->removeParticipationevenement($user);
   $entityManager = $this->getDoctrine()->getManager();
   $entityManager->persist($evenement);
   $entityManager->flush();

   // Redirect to the event page or wherever you want
   return $this->redirectToRoute('app_list_evenements_utilisateur', ['id' => $evenement->getId()]);
}





    #[Route('/recuperer-evenement/{id}', name: 'app_recuperer_evenement')]
    public function recupererEvenement(EvenementRepository $evenementRepository, $id, EntityManagerInterface $entityManager): Response
    {
        $evenement = $evenementRepository->find($id);
        $evenement->setEtat('x');
        $evenement->updateEtat($entityManager);
        $entityManager->flush();
        return $this->redirectToRoute('app_list_evenements');
    }


    #[Route('/update_evenement_rating/{id}', name: 'update_evenement_rating', methods: ['POST'])]
    public function updateEvenementRating(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        // Get the data from the request
        $rating = $request->query->get('rating');

        // Fetch the evenement entity from the database
        $evenement = $entityManager->getRepository(Evenement::class)->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('The evenement does not exist');
        }

        // Update the rating attribute of the evenement entity
        $evenement->setRating($rating);

        // Persist the changes to the database
        $entityManager->flush();

        // Return a JSON response indicating success
        return $this->json(['message' => 'Rating updated successfully']);
    }
    


  
}
