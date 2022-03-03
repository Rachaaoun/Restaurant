<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CodeType;
use Doctrine\ORM\EntityManagerInterface;


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
     * @Route("/confirm/{id}", name="app_confirm")
     */
    public function confirm( EntityManagerInterface $entityManager,AuthenticationUtils $authenticationUtils,$id,UserRepository $repo,Request $request): Response
    {
        $form = $this->createForm(CodeType::class);
        $form->handleRequest($request);
        $user=$repo->findOneById($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $code=$form->get('code')->getData();
            if($user->getCode() == $code ){
                $user->setIsVerified(true);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/confirm.html.twig', [
            
        'form'=>$form->createView()
    
    ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
