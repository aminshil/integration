<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Demande;
use App\Entity\Publication;
use App\Form\CommentType;
use App\Form\DemandeType;
use App\Form\PublicationType;
use App\Repository\CommentRepository;
use App\Repository\DemandeRepository;
use App\Repository\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BadWordFilter;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;


class PublicationController extends AbstractController
{
    
//    Afficher les page des integration des templates 
/*    
   #[Route('/Affiche', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'PublicationController',
        ]);
    }
    #[Route('/AfficheAdmin', name: 'app_admin')]
    public function indexAdmin(): Response
    {
        return $this->render('indexAdmin.html.twig', [
            'controller_name' => 'PublicationController',
        ]);
    }

*/

//    Afficher les page des publications de l'Admin  

    #[Route('/AfficheListeAdminDem', name: 'app_ListeAdminDem')]
    public function ListeAdminDem(DemandeRepository $rep, BadWordFilter $badWordFilter): Response
    {
        return $this->render('admin/demande/liste.html.twig', [
            'menu' => $rep->findall(),
            'badWordFilter' => $badWordFilter,'user'=>$this->getUser()
        ]);
    }
    #[Route('/AfficheListeAdminPub', name: 'app_ListeAdminPub')]
    public function ListeAdminPub(PublicationRepository $rep, BadWordFilter $badWordFilter): Response
    {
        return $this->render('admin/publication/liste.html.twig', [
            'menu' => $rep->findall(),
            'badWordFilter' => $badWordFilter,'user'=>$this->getUser()
            
        ]);
    }

    
    
//    Les fonctions des demandes
    
              //    Liste des demandes
    #[Route('/demande', name: 'app_demande')]
    public function demande(Request $req,DemandeRepository $rep, BadWordFilter $badWordFilter,PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $rep->paginationQuery(),
            $req->query->get('page', 1),
            2
        );
        return $this->render('publication/demande/liste.html.twig', [
            'pagination' => $pagination,
            'badWordFilter' => $badWordFilter,'user'=>$this->getUser() 
        ]);
    }

           //    Ajout des demandes

    #[Route('/adddemande', name: 'app_adddemande')]
    public function adddemande(ManagerRegistry $doctrine, Request $request): Response
    {       $user=$this->getUser();
        $ghassen = new Demande();
        $ghassen->setDemandeuser($user);
        $ghassen->setDateofdem(new \DateTime());
        $form = $this->createForm(DemandeType::class, $ghassen);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($ghassen);
            $em->flush();
            return $this->redirect('/demande');
        }
        return $this->render('publication/demande/add.html.twig', [
            'plat_form' => $form->createView(),'user'=>$this->getUser()
            
        ]);
    }
    
          //    Edit des demandes
    
 #[Route('/edit/{id}', name: 'demande_edit')]
    public function editdem(ManagerRegistry $ama,DemandeRepository $rep,int $id,Request $request): Response
    {
        $dem=$rep->find($id);
        $form=$this->createForm(DemandeType::class,$dem);
        $dem->setDemandeuser($this->getUser());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        { 
            $em= $ama->getmanager();
            $em->persist($dem);
            $em->flush();
            return $this->redirect('/demande');
        }
        return $this->render('publication/demande/edit.html.twig',
        ['formedit'=>$form->createView(),'user'=>$this->getUser()]);
    }

       //    Delete des demandes

    #[Route('/delete/{id}', name: 'demande_delete')]
    public function deletedem(DemandeRepository $rep,$id,ManagerRegistry $mr):Response
    {
        $dem=new demande();
        $dem=$rep->find($id);
        $em=$mr->getManager();
        $em->remove($dem);
        $em->flush();
        return $this->redirect('/demande');
    }
      
            //    Show des demandes

    #[Route('/demande/{id}', name: 'demande_show')]
    public function showdem(Request $request,DemandeRepository $platrepository, $id,EntityManagerInterface $em,BadWordFilter $badWordFilterService): Response
    { 
        $dem = new Demande();
        $dem = $platrepository->find($id);
        $user=$this->getUser();
        $commentaire = new Comment();
    $commentaire->setDateofcom(new \DateTime());
    $commentaire->setDemande($dem);
    $commentaire->setCommentpubdemuser($user);
    $form = $this->createForm(CommentType::class, $commentaire);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $commentaire->setDateofcom(new \DateTime());
        $commentaire->setComment($badWordFilterService->filterBadWords($commentaire->getComment()));
        $em->persist($commentaire);
        $em->flush();
        return $this->redirectToRoute('demande_show', [
            'id' => $dem->getId(),  
        ], Response::HTTP_SEE_OTHER);
    }
    $commentaires = $dem->getComments();
        return $this->render('publication/demande/show.html.twig', [
            'demande' => $platrepository->find($id),
            'commentaires' => $commentaires,
            'commentaire_form' => $form->createView(),'user'=>$user
        ]);
    }
    
      //    Modifier les commentaires des demandes

    #[Route('/modifiercommentairedem/{id}', name: 'app_modifier_commentairedem')]
    public function modifiercommentairedem(ManagerRegistry $manager,CommentRepository $CommentaireRepository,int $id,Request $request): Response
    {
        $commentaire=new Comment();
        $commentaire=$CommentaireRepository->find($id);
        $dem = $commentaire->getDemande();
        $form=$this->createForm(CommentType::class,$commentaire);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        { 
            $ide=$dem->getID();
            $em= $manager->getmanager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('demande_show', ['id' => $ide]);
        }
        return $this->render('publication/demande/comment.html.twig',
        ['formmodifiercommentaire'=>$form->createView(), 'demande' => $dem,'user'=>$this->getUser()]);
    }

            // Supprimer les commentaires des demandes

    #[Route('/supprimercommentaireutilisateurdem/{id}', name: 'app_supprimer_commentaire_utilisateurdem')]
    public function supprimercommentaireutilisateurdem(CommentRepository $CommentaireRepository,$id,ManagerRegistry $manager):Response
    {
        $commentaire=new Comment();
        $commentaire=$CommentaireRepository->find($id);
        $dem = $commentaire->getDemande();
        $ide=$dem->getID();
        $em=$manager->getManager();
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute('demande_show', ['id' => $ide,'user'=>$this->getUser()]);
    }














//    Les fonctions des demandes
         //    Liste des publications
    #[Route('/publication', name: 'app_pub')]
    public function pub(Request $req,PublicationRepository $rep , BadWordFilter $badWordFilter,PaginatorInterface $paginator): Response
    {
        $keyword = $req->query->get('keyword');
        if ($keyword) {
            $publications = $rep->searchByKeyword($keyword);
        } else {
            $publications = $rep->paginationQuery();
        }
        $pagination = $paginator->paginate(
            $publications,
            $req->query->get('page', 1),
            2
        );
        return $this->render('publication/publication/liste.html.twig', [
            'pagination' => $pagination,
            'badWordFilter' => $badWordFilter, 
            'keyword' => $keyword,'user'=>$this->getUser()
        ]);
    }

            //    Ajout des publications

    #[Route('/addpub', name: 'app_addpub')]
    public function addpub(ManagerRegistry $doctrine,Request $request ,FileUploader $fileUploader): Response
    {
        $user=$this->getUser();
        $ghassen = new Publication();
        $ghassen->setPublicationuser($user);
        $ghassen->setDateofpub(new \DateTime());
        $form= $this->createForm(PublicationType::class,$ghassen);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            
            $imagepubFile = $form->get('imagepub')->getData();
            if ($imagepubFile) {
                $imagepubFileName = $fileUploader->upload($imagepubFile);
                $ghassen->setImagepub($imagepubFileName);}
            $em=$doctrine->getManager();
            $em->persist($ghassen);
            $em->flush();
            return $this->redirect('/publication');
            }
            return $this->render('publication/publication/add.html.twig',[
                'plat_form' => $form->createView(),'user'=>$this->getUser()
            ]);
    }

     //    Edite des publications

#[Route('/editpub/{id}', name: 'pub_edit')]
    public function editpub(ManagerRegistry $ama,PublicationRepository $rep,int $id,Request $request,FileUploader $fileUploader): Response
    {
        $dem=new Publication();
        $dem=$rep->find($id);
        $form=$this->createForm(PublicationType::class,$dem);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        { 
            $imagepubFile = $form->get('imagepub')->getData();
            if ($imagepubFile) {
                $imagepubFileName = $fileUploader->upload($imagepubFile);
                $dem->setImagepub($imagepubFileName);}
           $em= $ama->getmanager();
            $em->persist($dem);
            $em->flush();
            return $this->redirect('/publication');
        }
        return $this->render('publication/publication/edit.html.twig',
        ['formeditpub'=>$form->createView(),'user'=>$this->getUser()]);
    }

    //    Delete des publications

  #[Route('/deletepub/{id}', name: 'pub_delete')]
    public function deletepub(PublicationRepository $rep,$id,ManagerRegistry $mr):Response
    {
        $dem=new Publication();
        $dem=$rep->find($id);
        $em=$mr->getManager();
        $em->remove($dem);
        $em->flush();
        return $this->redirect('/publication');
    }

    //    Edit des publications

     #[Route('/publication/{id}', name: 'pub_show')]
    public function showpub(Request $request,PublicationRepository $platrepository, $id,EntityManagerInterface $em,BadWordFilter $badWordFilterService): Response
    {
        $user=$this->getUser();
        $pub = new Publication();
        $pub = $platrepository->find($id);
    $commentaire = new Comment();
    $commentaire->setDateofcom(new \DateTime());
    $commentaire->setPublication($pub);
    $commentaire->setCommentpubdemuser($user);
    $form = $this->createForm(CommentType::class, $commentaire);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $commentaire->setDateofcom(new \DateTime());
        $commentaire->setComment($badWordFilterService->filterBadWords($commentaire->getComment()));
        $em->persist($commentaire);
        $em->flush();
        return $this->redirectToRoute('pub_show', [
            'id' => $pub->getId(),'user'=>$user
        ], Response::HTTP_SEE_OTHER);
    }
    $commentaires = $pub->getComments();
      return $this->render('publication/publication/show.html.twig', [
            'publication' => $platrepository->find($id),
            'commentaires' => $commentaires,
        'commentaire_form' => $form->createView(),'user'=>$this->getUser()
        ]);
    }

     //    Modifier les commentaires des publications

 #[Route('/modifiercommentairepub/{id}', name: 'app_modifier_commentairepub')]
    public function modifiercommentairepub(ManagerRegistry $manager,CommentRepository $CommentaireRepository,int $id,Request $request): Response
    {
        $commentaire=new Comment();
        $commentaire=$CommentaireRepository->find($id);
        $pub = $commentaire->getPublication();
        $form=$this->createForm(CommentType::class,$commentaire);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        { 
            $ide=$pub->getID();
            $em= $manager->getmanager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('pub_show', ['id' => $ide ,'user'=>$this->getUser()]);
        }
        return $this->render('publication/publication/comment.html.twig',
        ['formmodifiercommentaire'=>$form->createView(), 'publication' => $pub,'user'=>$this->getUser()]);
    }

    //    Supprimer les commentaires des publications


    #[Route('/supprimercommentaireutilisateurpub/{id}', name: 'app_supprimer_commentaire_utilisateurpub')]
    public function supprimercommentaireutilisateurpub(CommentRepository $CommentaireRepository,$id,ManagerRegistry $manager):Response
    {
        $commentaire=new Comment();
        $commentaire=$CommentaireRepository->find($id);
        $pub = $commentaire->getPublication();
        $ide=$pub->getID();
        $em=$manager->getManager();
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute('pub_show', ['id' => $ide,'user'=>$this->getUser()]);
    }



    /* test des categorie 
    #[Route('/test', name: 'app_addtest')]
    public function addtest(ManagerRegistry $doctrine, Request $request): Response
    {
        $ghassen = new Category();
        $form = $this->createForm(CategoryType::class, $ghassen);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($ghassen);
            $em->flush();
    
           
        }
    
        return $this->render('test.html.twig', [
            'f' => $form->createView(),
        ]);
    } */

 
  
}