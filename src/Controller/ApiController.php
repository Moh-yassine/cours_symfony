<?php

namespace App\Controller;

use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api", name: "api_")]
final class ApiController extends AbstractController
{
  #[Route("/api", name: "app_api")]
  public function index(): Response
  {
    return $this->render("api/index.html.twig", [
      "controller_name" => "ApiController",
    ]);
  }

  #[Route("/offers", name: "list", methods: ["GET"])]
  public function list(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
  {
    $offers = $em->getRepository(Offer::class)->findAll();
    $data = $serializer->serialize($offers, "json", ["groups" => "offer:read"]);

    return new JsonResponse($data, 200, [], true);
  }

  #[Route("/offers/{id}", name: "show", methods: ["GET"])]
  public function show(Offer $offer, SerializerInterface $serializer): JsonResponse
  {
    $data = $serializer->serialize($offer, "json", ["groups" => "offer:read"]);

    return new JsonResponse($data, 200, [], true);
  }
#[Route("/offers", name: "store", methods: ["POST"])]
  public function store(
    Request $request,
    SerializerInterface $serializer,
    EntityManagerInterface $em
  ): JsonResponse {
    $offer = $serializer->deserialize($request->getContent(), Offer::class, "json");
    $user = $this->getUser();
    $offer->setRecruiter($user);
    $em->persist($offer);
    $em->flush();

    return new JsonResponse("Offre crée avec succées", 201);
  }

  #[Route("/offers/{id}", name: "update", methods: ["PUT"])]
  public function update(
    Offer $offer,
    Request $request,
    SerializerInterface $serializer,
    EntityManagerInterface $em
  ): JsonResponse {
    $serializer->deserialize($request->getContent(), Offer::class, "json", [
      "object_to_populate" => $offer,
    ]);
    $em->flush();

    return new JsonResponse("Offre mis à jour avec succées", 201);
  }

  #[Route("/offers/{id}", name: "delete", methods: ["DELETE"])]
  public function delete(Offer $offer, EntityManagerInterface $em): JsonResponse
  {
    $em->remove($offer);
    $em->flush();

    return new JsonResponse("La suppression a été effectuée avec succès.", 204);
  }
}
