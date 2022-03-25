<?php

namespace App\Controller;

use App\Entity\Cartefidelite;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use \Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $twilio;
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $file=$form->get('photo')->getData();
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move($this->getParameter('images_directory'),$fileName);
            }catch(FileException $e){
    
            }
          
            $user->setPhoto($fileName);


            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $cartefidelite = new Cartefidelite();
            $num=random_int(11111111,99999999);
            $time = new \DateTime ('+3 year');
            $cartefidelite->setNum((String) $num);
            $cartefidelite->setNbpts(0);
            $cartefidelite->setPeriodevalidation("5mois");
            $cartefidelite->setDateexpiration($time);
            $entityManager->persist($cartefidelite);
            $entityManager->flush();
            $user->setCarte($cartefidelite);

            $code=random_int(1000,99999);

            $user->setCode($code);
            $entityManager->persist($user);
            $entityManager->flush();

                 $message = (new \Swift_Message('Hello Email'))
                ->setFrom('racha.aoun@esprit.tn')
                ->setTo($form->get('email')->getData())
                ->setBody(
                    $this->renderView(
                        // templates/hello/email.txt.twig
                        'user/email.txt.twig',
                        ['name' => 'racha' , 'code'=>$code]
                    )
                )
                 ;
            $mailer->send($message);
            $sid = "ACcb357d4a35b4d0fddf30a204405908cb"; // Your Account SID from www.twilio.com/console
            $token = "cb3211fa23870408880dbd187ad73291"; // Your Auth Token from www.twilio.com/console
            //cb3211fa23870408880dbd187ad73291
            $client = new Client($sid, $token);
            
             $message = $client->messages 
             ->create("+21655904764", // to 
                      array(  
                          "messagingServiceSid" => "MG6906411a9eaa484b6a13b6707b84f124",      
                          "body" => "your account is created you can log in " 
                      ) 
             ); 
            print($message->sid);

            return $this->redirectToRoute('app_confirm',['id'=>$user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),

        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }



}
