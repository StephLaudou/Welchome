<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Form\HousingType;
use App\Repository\HousingRepository;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/housing")
 */
class HousingController extends AbstractController
{
    /**
     * @Route("/", name="housing_index", methods={"GET"})
     * @param HousingRepository $housingRepository
     * @return Response
     */
    public function index(HousingRepository $housingRepository): Response
    {
        $housings = $this->getDoctrine()->getRepository('App\Entity\Housing')->findAll();
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($housings,'json');

        $response = new Response($data);
        $response->headers->set('Content-Type','application/json');

        return $response;
    }

    /**
     * @Route("/new", name="housing_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $housing = new Housing();
        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($housing);
            $entityManager->flush();

            return $this->redirectToRoute('housing_index');
        }

        return $this->render('housing/new.html.twig', [
            'housing' => $housing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="housing_show", methods={"GET"})
     * @param Housing $housing
     * @return Response
     */
    public function show(Housing $housing): Response
    {
        return $this->render('housing/show.html.twig', [
            'housing' => $housing,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="housing_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Housing $housing
     * @return Response
     */
    public function edit(Request $request, Housing $housing): Response
    {
        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('housing_index', [
                'id' => $housing->getId(),
            ]);
        }

        return $this->render('housing/edit.html.twig', [
            'housing' => $housing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="housing_delete", methods={"DELETE"})
     * @param Request $request
     * @param Housing $housing
     * @return Response
     */
    public function delete(Request $request, Housing $housing): Response
    {
        if ($this->isCsrfTokenValid('delete'.$housing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($housing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('housing_index');
    }
}