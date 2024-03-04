<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\AdminType;
use App\Form\AdminEditeType;
use App\Form\PropertySearchType;
use App\Entity\PropertySearch;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class AdminController extends AbstractController
{
   /* #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
*/
private $passwordEncoder;

public function __construct(UserPasswordEncoderInterface $passwordEncoder)
{
    $this->passwordEncoder = $passwordEncoder;
}

  #[Route('/afficher_admin', name: 'app_afficher_admin')]
  public function affciher(UserRepository $repository, Request $req): Response
  {
      $users=$repository ->findAll();//select tous
     
  return $this->render("admin/index.html.twig",[
      'user'=>$users]);
  }

  #[Route('/addadmin', name: 'app_addadmin')]
  public function addformulaire( ManagerRegistry $ManagerRegistry,Request $req )
  {
    
  $en =$ManagerRegistry->getManager();
  $users=new User();
  $form=$this->createform(AdminType::class,$users);
  $form->handleRequest($req);
  if ($form->isSubmitted() and $form->isValid())
  {
    $users->setPassword($this->passwordEncoder->encodePassword($users, $users->getPassword()));
    $selectedRoles = $form->get('roles')->getData();
    $users->setRoles($selectedRoles);
    $users->setIsBanned(false);
  $en->persist($users);
  $en->flush();
  return $this->redirectToRoute('app_afficher_admin', ['id' => $users->getId()]);
  //return $this->redirectToRoute("app_signup");
  
  }
      return $this->renderForm("admin/addadmin.html.twig",[
          'form'=>$form]);
  }

  #[Route('/edditadmin/{id}=', name: 'app_edditadmin')]
  public function editadmin($id, UserRepository $repository1, ManagerRegistry $ManagerRegistry, Request $req): Response 
  {
  
    //var_dump($id) . die();
    $users=new User();
    
    $en =$ManagerRegistry->getManager();
    $dataid=$repository1->find($id);
    $form=$this->createform(AdminEditeType::class,$dataid);
    $form->handleRequest($req);
  if ($form->isSubmitted() and $form->isValid())
  {
    $users->setPassword($this->passwordEncoder->encodePassword($users, $users->getPassword()));
    
  $en->persist($dataid);
  $en->flush();
  return $this->redirectToRoute('app_afficher_admin' , ['id' => $dataid->getId()]);
  }
      return $this->renderForm("admin/editeadmin.html.twig",[
          'formedit' => $form]);
  }



  #[Route('/deleteadmin/{id}', name: 'app_deleteadmin')]
  public function delete($id,UserRepository $repository2,ManagerRegistry $ManagerRegistry,Request $req): Response
  { $en =$ManagerRegistry->getManager();
      $fin=$repository2->find($id);
      $en->remove($fin);
      $en->flush();
      return $this->redirectToRoute("app_afficher_admin");
  }


  #[Route('/searchbyname', name: 'app_searchbyname')]
  public function searchby(Request $request , UserRepository $userRepository):Response
  {
    $propertySearch = new PropertySearch();
    $form= $this->createForm(PropertySearchType::class,$propertySearch);
    $form->handleRequest($request);
   //initialement le tableau des articles est vide, 
   //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
    $users= [];
    
   if($form->isSubmitted() && $form->isValid()) {
   //on récupère le nom d'article tapé dans le formulaire
    $name= $propertySearch->getName();   
    if ($name!="") 
      //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
      $users= $this->getDoctrine()->getRepository(User::class)->findBy(['name' => $name] );
    else   
      //si si aucun nom n'est fourni on affiche tous les articles
      $users= $this->getDoctrine()->getRepository(User::class)->findAll();
   }
    return  $this->render('admin/index.html.twig',[ 'form1' =>$form->createView(), 'user' => $users]);  
  }


  #[Route('/banUser/{id}', name: 'ban_user', methods: ['GET', 'POST'])]
  public function banUser(Request $request, UserRepository $userRepository,int $id): Response
  {
      // Retrieve the search query from the request
      $user = $userRepository->find($id);

      // Perform the search operation based on the query
       $userRepository->banUnbanUser($user);

      // Return the search results as JSON response
      return $this->redirectToRoute('app_afficher_admin');
  }
  #[Route('/users/sort', name: 'user_sort', methods: ['POST'])]
  public function sortUsers(Request $request): JsonResponse
  {
      // Récupérer les données de tri depuis la requête POST
      $sortOption = $request->request->get('sortOption');
      $sortDirection = $request->request->get('sortDirection');
  
      // Implémentez votre logique de tri ici en fonction de $sortOption et $sortDirection
      if ($sortOption === 'name') {
          if ($sortDirection === 'asc') {
              $sortedUsers = $this->getDoctrine()->getRepository(User::class)->findBy([], ['name' => 'ASC']);
          } else {
              $sortedUsers = $this->getDoctrine()->getRepository(User::class)->findBy([], ['name' => 'DESC']);
          }
      } else {
          // Par défaut, triez par nom dans l'ordre ascendant
          $sortedUsers = $this->getDoctrine()->getRepository(User::class)->findBy([], ['name' => 'ASC']);
      }
  
      // Renvoyer les résultats triés sous forme de réponse JSON
      return $this->json($sortedUsers);
  }

 
  #[Route('/users/search', name: 'user_search', methods: ['GET'])]
  public function search(Request $request, UserRepository $userRepository): JsonResponse
  {
      // Récupérer le terme de recherche depuis la requête
      $searchTerm = $request->query->get('query');

      // Vérifier si le terme de recherche est vide
      if (!$searchTerm) {
          return new JsonResponse(['message' => 'Veuillez fournir un terme de recherche.'], JsonResponse::HTTP_BAD_REQUEST);
      }

      // Effectuer la recherche dans le référentiel UserRepository
      $users = $userRepository->findBySearchTerm($searchTerm);

      // Retourner les résultats de la recherche en tant que réponse JSON
      return $this->json($users);
  }

}
