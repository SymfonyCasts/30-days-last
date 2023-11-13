<?php

namespace App\Controller;

use App\Entity\Planet;
use App\Form\Planet1Type;
use App\Repository\PlanetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planet/crud')]
class PlanetCrudController extends AbstractController
{
    #[Route('/', name: 'app_planet_crud_index', methods: ['GET'])]
    public function index(PlanetRepository $planetRepository): Response
    {
        return $this->render('planet_crud/index.html.twig', [
            'planets' => $planetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_planet_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planet = new Planet();
        $form = $this->createForm(Planet1Type::class, $planet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planet);
            $entityManager->flush();

            return $this->redirectToRoute('app_planet_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planet_crud/new.html.twig', [
            'planet' => $planet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_planet_crud_show', methods: ['GET'])]
    public function show(Planet $planet): Response
    {
        return $this->render('planet_crud/show.html.twig', [
            'planet' => $planet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_planet_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Planet $planet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Planet1Type::class, $planet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_planet_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planet_crud/edit.html.twig', [
            'planet' => $planet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_planet_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Planet $planet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($planet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_planet_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
