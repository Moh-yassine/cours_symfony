<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{

    #[Route("/api", name: "app_api")]
  public function index(): Response
  {
    return $this->render("api/index.html.twig", [
      "controller_name" => "ApiController",
    ]);
  }
  
    #[Route('/offers', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em) :JsonResponse
    {
       $offers = $em->getRepository(Offer::class)->findAll();
       $data = array_map(fn($offer) => [
        'id' => $offer->getId(),
        'title' => $offer->getTitle(),
        'description' => $offer->getDescription(),
        'recruiter' => $offer->getRecruiter()->getUsername()
       ], $offers);

       return $this->json($data);
    }
}
