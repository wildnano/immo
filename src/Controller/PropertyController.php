<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\Contact;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PropertyController
 * @package App\Controller
 */
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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
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


        //$properties = $this->repository->findAllVisible();
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            12/*page number*/
        );
        //dump($property);
        //$property[0]->setSold(true);
        //$this->em->flush();

        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties' => $properties,
            'form' => $form->createView()


        ]);
    }

    /**
     * @Route("/properties/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     *
     */
    //public function show($slug, $id): Response
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
    {
        //$property = $this->repository->find($id);


        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Email sent');
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ]);
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties',
            'form' => $form->createView()
        ]);
    }

}