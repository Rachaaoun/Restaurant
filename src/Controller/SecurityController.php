<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }



    /**
     * @Route("/signin" ,  methods={"GET", "POST"})
     * @param UserRepository $userRepository
     */
    public function singnInAction (Request $request,UserRepository $userRepository,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $email=$request->query->get("email");
        $password= $request->query->get("password");
        $user = $userRepository->findOneByEmail($email) ;
    //   $password=  $userPasswordEncoder->encodePassword(
    //         $user,
    //         $request->query->get("password")
    //   );
        if($user)
        {
            if($password == $user->getPassword())
            {
                $serializer= new Serializer ([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user); 
                return new   JsonResponse ($formatted); 
            }
            else {
                return new JsonResponse("failed"); 
            }
        }
        else 
        {
            return new JsonResponse("failed");
        }
    }
}
