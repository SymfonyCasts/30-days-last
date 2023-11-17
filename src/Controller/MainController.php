<?php

namespace App\Controller;

use App\Repository\PlanetRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(VoyageRepository $voyageRepository, PlanetRepository $planetRepository, Request $request): Response
    {
        $query = $request->query->get('query');
        $searchPlanets = array ($request->query->all()['planets'] ?? []);
        $voyages = $voyageRepository->findBySearch($query, $searchPlanets);

        return $this->render('main/homepage.html.twig', [
            'voyages' => $voyages,
            'planets' => $planetRepository->findAll(),
            'searchPlanets' => $searchPlanets,
        ]);
    }
}
