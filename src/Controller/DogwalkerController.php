<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\DogWalker;
use App\Repository\DogWalkerRepository;

class DogwalkerController extends AbstractController
{
    #[Route('/', name: 'dogwalkers')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/getwalkers/{zipCode}', name: 'getwalkers')]
    public function getWalkers(DogWalkerRepository $dogWalkerRepository, string $zipCode): Response
    {
        $walkers = $dogWalkerRepository->findBy(['zipCode' => $zipCode]);

        if (empty($walkers)) {
            return $this->render('default/no_walkers.html.twig', [
                'message' => 'No dog walkers found in your area.',
            ]);
        }
        return $this->json($walkers);
        // return $this->render('default/index.html.twig', [
        //     'controller_name' => 'DefaultController',
        //     'walkers' => $walkers,
        // ]);
    }
}