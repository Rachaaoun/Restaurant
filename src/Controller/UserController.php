<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationFormType;
use App\Data\SearchData;
use App\Entity\Cartefidelite;
use App\Form\SearchForm;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    const ATTRIBUTES_TO_SERIALIZE =['id','nom','prenom','email','password','photo','cin'];

    /**
     * @Route("/profile", name="profile",  methods={"GET", "POST"})
     */
    public function profile(Request $request,  EntityManagerInterface $entityManager,UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $user=$this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file=$form->get('photo')->getData();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){
    
            }
          
            $user->setPhoto($fileName);
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            $entityManager->flush();

        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository,Request $request): Response
    {
             
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        $users= $userRepository->findSearch($data);
       

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
        ]);
    }
    

    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager ,UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
    
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/approve", name="user_approve", methods={"GET", "POST"})
     */
    public function Approve(Request $request, User $user, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
       
        $user->setIsVerified(true);

            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        
        return $this->render('user/index.html.twig', [
     
            'users' => $userRepository->findAll(),
      
        ]);
    }

    /**
     * @Route("/ajouter/utilisateur" , name="utilisateur_ajouter" ,  methods={"GET", "POST"})
     */
    public function ajouter(Request $request,SerializerInterface $serializer)
    {
      
        $user = new User();
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $password=$request->query->get('password');
        $photo=$request->query->get('photo');
        $email=$request->query->get('email');
        $cin=$request->query->get('cin');
        $em=$this->getDoctrine()->getManager();
        $user->setPrenom($prenom);
        $user->setNom($nom);
        $user->setCin($cin);
        $user->setEmail($email);
        $user->setIsVerified(true);
        $user->setPassword($password);
        $user->setPhoto($photo);

        $cartefidelite = new Cartefidelite();
        $num=random_int(11111111,99999999);
            $time = new \DateTime ('+3 year');
            $cartefidelite->setNum((String) $num);
            $cartefidelite->setNbpts(0);
            $cartefidelite->setPeriodevalidation("5mois");
            $cartefidelite->setDateexpiration($time);
            $em->persist($cartefidelite);
           
            $user->setCarte($cartefidelite);
            $em->persist($user);
       
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($user);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/utilisateur/list")
     * @param UserRepository $repo
     */
    public function getList(UserRepository $repo,SerializerInterface $serializer):Response{
     
                $users=$repo->findAll();
                $json=$serializer->serialize($users,'json', ['groups' => ['user']]);
        
        
                return $this->json(['user'=>$users],Response::HTTP_OK,[],[
                    'attributes'=>self::ATTRIBUTES_TO_SERIALIZE
                ]);
        
    }
    
    /**
     * @Route("/detail/{id}",name="user_detail")
     */
    public function userProfile(UserRepository $repo,$id,SerializerInterface $serializer){
        $user=$repo->findById($id);
      
        $json=$serializer->serialize($user,'json', ['groups' => ['user']]);


        return $this->json(['user'=>$user],Response::HTTP_OK,[],[
            'attributes'=>self::ATTRIBUTES_TO_SERIALIZE
        ]);

    }

    /**
     * @Route("/edit/profile/{id}" , name="utilisateur_modifier" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function editProfile(Request $request,SerializerInterface $serializer,$id,UserRepository $repo)
    {
        $user=$repo->findOneById($id);
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $password=$request->query->get('password');
        $photo=$request->query->get('photo');
        $email=$request->query->get('email');
        $cin=$request->query->get('cin');
        $em=$this->getDoctrine()->getManager();
        $user->setPrenom($prenom);
        $user->setNom($nom);
        $user->setCin($cin);
        $user->setEmail($email);
        $user->setIsVerified(true);
        $user->setPassword($password);
        $user->setPhoto($photo);
        $em->persist($user);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($user);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/utilisateur/delete", name="supprimer_utilisateur")
     */
    public function supprimerUtilisateur(Request $request, UserRepository $repo): Response
    {

        $id =$request->get("id");
        $em=$this->getDoctrine()->getManager();

     $d=   $repo->find($id);

        if($d != null){
            $em->remove($d);
            $em->flush();
            $serializer=new Serializer([new ObjectNormalizer()]);
            $formatted=$serializer->normalize("utilsateur a eté supprimeé");
            return new JsonResponse($formatted);
        }

       return  new JsonResponse("Id Invalide");
    }
   
}
