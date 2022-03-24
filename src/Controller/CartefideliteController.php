<?php

namespace App\Controller;

use App\Entity\Cartefidelite;
use App\Form\CartefideliteType;
use App\Repository\CartefideliteRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/cartefidelite")
 */
class CartefideliteController extends AbstractController
{

    const ATTRIBUTES_TO_SERIALIZE =['id','num','nbpts','periodevalidation','dateexpiration'];


   
    /**
     * @Route("/", name="cartefidelite_index", methods={"GET"})
     */
    public function index(CartefideliteRepository $cartefideliteRepository): Response
    {
        return $this->render('cartefidelite/index.html.twig', [
            'cartefidelites' => $cartefideliteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="cartefidelite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cartefidelite = new Cartefidelite();
        $form = $this->createForm(CartefideliteType::class, $cartefidelite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
       
            $entityManager->persist($cartefidelite);
            $entityManager->flush();

            return $this->redirectToRoute('cartefidelite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cartefidelite/new.html.twig', [
            'cartefidelite' => $cartefidelite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cartefidelite_show", methods={"GET"})
     */
    public function show(Cartefidelite $cartefidelite): Response
    {
        return $this->render('cartefidelite/show.html.twig', [
            'cartefidelite' => $cartefidelite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cartefidelite_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Cartefidelite $cartefidelite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CartefideliteType::class, $cartefidelite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('cartefidelite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cartefidelite/edit.html.twig', [
            'cartefidelite' => $cartefidelite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cartefidelite_delete", methods={"POST"})
     */
    public function delete(Request $request, Cartefidelite $cartefidelite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartefidelite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cartefidelite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cartefidelite_index', [], Response::HTTP_SEE_OTHER);
    }
    
     /**
     * @Route("/ajouter/cartefidelite" , methods={"GET", "POST"})
     */
    public function ajouter(Request $request,SerializerInterface $serializer)
    {
      
        $carte = new Cartefidelite();
        $num=$request->query->get('num');
       // $dateexpiration=$request->query->get('dateexpiration');
        $time = strtotime($dateexpiration);

        $newformat = date('Y-m-d',$time);
        $time = new \DateTime ('+3 year');
        $nbpts=$request->query->get('nbpts');
        $periodevalidation=$request->query->get('periodevalidation');
        $date = date_create_from_format('j/m/Y', $dateexpiration); // Your original format 
        $formatted_date = date_format($date, 'Y-m-d');
        $em=$this->getDoctrine()->getManager();
        $carte->setNum($num);
        $carte->setNbpts($nbpts);
        $carte->setPeriodevalidation($periodevalidation);
        $carte->setDateexpiration($time);
        
        $em->persist($carte);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($carte);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/cartefidelite/list")
     * @param CartefideliteRepository $repo
     */
    public function getList(CartefideliteRepository $repo,SerializerInterface $serializer):Response{
     
        $cartes=$repo->findAll();
        $json=$serializer->serialize($cartes,'json', ['groups' => ['carte']]);


        return $this->json(['carte'=>$cartes],Response::HTTP_OK,[],[
            'attributes'=>self::ATTRIBUTES_TO_SERIALIZE
        ]);

}



     /**
     * @Route("/edit/cartefidelite/{id}" , methods={"GET", "POST"})
     */
    public function editCarte(Request $request,SerializerInterface $serializer,$id,CartefideliteRepository $repo)
    {
      
        $carte = $repo->findOneById($id);
        $num=$request->query->get('num');
       // $dateexpiration=$request->query->get('dateexpiration');
        $time = strtotime($dateexpiration);

        $newformat = date('Y-m-d',$time);
        $time = new \DateTime ('+3 year');
        $nbpts=$request->query->get('nbpts');
        $periodevalidation=$request->query->get('periodevalidation');
        $date = date_create_from_format('j/m/Y', $dateexpiration); // Your original format 
        $formatted_date = date_format($date, 'Y-m-d');
        $em=$this->getDoctrine()->getManager();
        $carte->setNum($num);
        $carte->setNbpts($nbpts);
        $carte->setPeriodevalidation($periodevalidation);
        $carte->setDateexpiration($time);
        
        $em->persist($carte);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($carte);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/carte/delete", name="supprimer_cartes")
     */
    public function supprimerUtilisateur(Request $request, CartefideliteRepository $repo): Response
    {

        $id =$request->get("id");
        $em=$this->getDoctrine()->getManager();

     $d=   $repo->find($id);

        if($d != null){
            $em->remove($d);
            $em->flush();
            $serializer=new Serializer([new ObjectNormalizer()]);
            $formatted=$serializer->normalize("carte a eté supprimeé");
            return new JsonResponse($formatted);
        }

       return  new JsonResponse("Id Invalide");
    }

    /**
     * @Route("/modifier/carte/{id}" , name="carte_edit" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function carteEdit(Request $request,SerializerInterface $serializer,$id,CartefideliteRepository $repo)
    {
        $carte=$repo->findOneById($id);
        $num=$request->query->get('num');
        $nbpts=$request->query->get('nbpts');
        $periodevalidation=$request->query->get('periodevalidation');
        $dateexpiration=$request->query->get('dateexpiration');
       
        $em=$this->getDoctrine()->getManager();
      
        $carte->setNum($num);
       
        $carte->setNbpts($nbpts);
        $carte->setPeriodevalidation($periodevalidation);
        $carte->setDateexpiration($carte->getDateexpiration());
        
        $em->persist($carte);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($carte);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/activate/carte")
     */
    public function activate(Request $request,CartefideliteRepository $repo){
        $id=$request->query->get('id');
        $carte=$repo->findOneById($id);
        $date=$carte->getDateexpiration();
        $em=$this->getDoctrine()->getManager();
        //$todaydate = $this->getDate();
        $todaydate= new \DateTime('now');
        //dd($todaydate,$date);
        $datee=$date->format('Y-m-d H:i:s');
        $datetoday = new \DateTime('now');
        $result = $datetoday->format('Y-m-d H:i:s');
        $result2 = $date->format('Y-m-d H:i:s');
        
    $dateTimestamp2 = strtotime( date("Y-m-d") );
  //dd($result,$result2);

        if ($result > $result2){
            $time = new \DateTime ('+3 year');
            $carte->setDateexpiration($time);
            $em->persist($carte);
            $em->flush();
            $serializer=new Serializer([new ObjectNormalizer()]);
            $formatted=$serializer->normalize($carte);
            return new JsonResponse("true");
        }

        else 
        return new JsonResponse("false");

    }

}
