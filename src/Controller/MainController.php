<?php

namespace App\Controller;

use App\Repository\PlanetRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(VoyageRepository $voyageRepository, PlanetRepository $planetRepository): Response
    {
        return $this->render('main/homepage.html.twig', [
            'voyages' => $voyageRepository->findAll(),
            'planets' => $planetRepository->findAll(),
        ]);
    }
}
