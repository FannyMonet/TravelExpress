<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\TravelType;
use App\Form\SearchFormType;
use App\Repository\TravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Route("/travel")
 */
class TravelController extends AbstractController
{
    /**
     * @Route("/", name="travel_index", methods={"GET", "POST"})
     */
    public function index(Request $request, TravelRepository $travelRepository): Response
    {
        $form = $this->createForm(SearchFormType::class, array('method' => 'GET'));
        $form->handleRequest($request);
        $travels = $travelRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $travels = $travelRepository->findByDepartureAndArrival($form->get('departure')->getData(), $form->get('arrival')->getData());
        }
        return $this->render('travel/index.html.twig', [
            'travels' => $travels,
            'searchForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="travel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $travel = new Travel();
        $form = $this->createForm(TravelType::class, $travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $travel->setDriver($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($travel);
            $entityManager->flush();

            return $this->redirectToRoute('travel_index');
        }

        return $this->render('travel/new.html.twig', [
            'travel' => $travel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="travel_show", methods={"GET", "POST"})
     */
    public function show(Travel $travel, Request $request): Response
    {
        $price = 0;
        $choices = array();
        $i = 1;
        for($i=1; $i<=$travel->getNumberOfPassengers(); $i++)
        {
            array_push($choices, $i);
        }

        $numberPassengersForm = $this->createFormBuilder()
        ->add('numberOfPassengers', ChoiceType::class, [
            'choices' => $choices,
            'choice_label' => function($choice, $key, $value) {
                return $value;
            },
        ])
        ->add('submit', SubmitType::class,
            [
              'label' => 'Book',
              'attr' => [
                  'class' => 'btn btn btn-primary',
              ]
            ])
        ->getForm();

        $numberPassengersForm->handleRequest($request);

        if ($numberPassengersForm->isSubmitted() && $numberPassengersForm->isValid()) {
            $numberOfPlaces =  $numberPassengersForm->get('numberOfPassengers')->getData();
            if($travel->getPassengers()->contains($this->getUser()))
            {
                $this->addFlash('warning', 'You have already booked this trip.');
            }
            else if($travel->getPassengers()->count() +$numberOfPlaces <= $travel->getNumberOfPassengers())
            {
                $travel->addPassenger($this->getUser());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($travel);
                $entityManager->flush();
                $price = $numberOfPlaces * $travel->getPrice();
                $this->addFlash('success', 'You successfully booked ' . $numberOfPlaces . ' place(s) in this trip.');
            }
            else
                $this->addFlash('error', 'This trip is full');
        }

        return $this->render('travel/show.html.twig', [
            'travel' => $travel,
            'passengersForm' => $numberPassengersForm->createView(),
            'price' => $price
        ]);
    }

    /**
     * @Route("/{id}/edit", name="travel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Travel $travel): Response
    {
        $form = $this->createForm(TravelType::class, $travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('travel_index', [
                'id' => $travel->getId(),
            ]);
        }

        return $this->render('travel/edit.html.twig', [
            'travel' => $travel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="travel_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Travel $travel): Response
    {
        if ($this->isCsrfTokenValid('delete'.$travel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($travel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('travel_index');
    }
}
