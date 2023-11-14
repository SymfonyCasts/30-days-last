<?php

namespace App\Controller;

use App\Entity\Planet;
use App\Form\PlanetType;
use App\Repository\PlanetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planet')]
class PlanetController extends AbstractController
{
    #[Route('/', name: 'app_planet_index', methods: ['GET'])]
    public function index(PlanetRepository $planetRepository): Response
    {
        return $this->render('planet/index.html.twig', [
            'planets' => $planetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_planet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planet = new Planet();
        $form = $this->createForm(PlanetType::class, $planet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planet);
            $entityManager->flush();

            $this->addFlash('success', 'Planet created - wow!');

            return $this->redirectToRoute('app_planet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planet/new.html.twig', [
            'planet' => $planet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_planet_show', methods: ['GET'])]
    public function show(Planet $planet): Response
    {
        return $this->render('planet/show.html.twig', [
            'planet' => $planet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_planet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Planet $planet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanetType::class, $planet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Planet updated!');

            return $this->redirectToRoute('app_planet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planet/edit.html.twig', [
            'planet' => $planet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_planet_delete', methods: ['POST'])]
    public function delete(Request $request, Planet $planet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($planet);
            $entityManager->flush();

            $this->addFlash('success', 'Planet deleted! Yikes!');
        }

        return $this->redirectToRoute('app_planet_index', [], Response::HTTP_SEE_OTHER);
    }
}
