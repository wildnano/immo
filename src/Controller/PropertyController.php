<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/properties", name="property.index")
     * @return Response
     */
    public function index(): Response
    {
//        $property = new Property();
//        $property->setTitle('My first property')
//            ->setPrice(300000)
//            ->setRooms(5)
//            ->setBedrooms(4)
//            ->setDescription('A little description,')
//            ->setSurface(120)
//            ->setFloor(2)
//            ->setHeat(0)
//            ->setCity('Los Angeles')
//            ->setAddress('1632 Bellevue Avenue')
//            ->setPostalCode(90026);
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($property);
//        $em->flush();

        
        $property = $this->repository->findAllVisible();
        //dump($property);
        //$property[0]->setSold(true);
        //$this->em->flush();

        return $this->render('property/index.html.twig', [
           'current_menu' =>'properties'
        ]);
    }

    /**
     * @Route("/properties/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     *
     */
    //public function show($slug, $id): Response
    public function show(Property $property, string $slug): Response
    {
        //$property = $this->repository->find($id);
        if($property->getSlug() !== $slug){
               return $this->redirectToRoute('property.show', [
                    'id' => $property->getId(),
                    'slug' => $property->getSlug()
            ], 301);
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' =>'properties'
        ]);
    }

    /**
     * @Route("properties/text", name="property.text")
     * @return Response
     */
    public function test() {
        return $this->render('property/test.html.twig');
    }
    
}