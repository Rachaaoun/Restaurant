<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/approved", name="approved")
     */
    public function approved(): Response
    {
        return $this->render('home/approved.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function indexadmin(): Response
    {
        return $this->render('home/indexadmin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
