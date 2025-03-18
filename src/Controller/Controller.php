<?php

namespace App\Controller;

use App\Entity\Candidacy;
use App\Entity\Offer;
use App\Form\CandidacyType;
use App\Form\OfferType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Controller extends AbstractController
{
  #[Route("/", name: "app_")]
  public function index(): Response
  {
    $message = "Message test";
    $offre = ["Offre 1", "Offre 2"];
    $offreString = implode(", ", $offre);
    return $this->render("/index.html.twig", [
      "controller_name" => "Controller",
      "message" => $message,
      "offre" => $offreString,
    ]);
  }

  #[Route("/dashboard", name: "app_")]
  public function dashboard(EntityManagerInterface $entityManager): Response
  {
    $offers = $entityManager->getRepository(Offer::class)->findAll();
    $candidacies = $entityManager->getRepository(Candidacy::class)->findAll();
    return $this->render("app/dashboard.html.twig", [
      "offers" => $offers,
      "candidacies" => $candidacies,
    ]);
  }

  #[Route("/add-offer", name: "add_offer")]
  public function addOffer(EntityManagerInterface $entityManager, Request $request): Response
  {
    $offer = new Offer();

    $form = $this->createForm(OfferType::class, $offer);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $offer->setRecruiter($this->getUser());
      $entityManager->persist($offer);
      $entityManager->flush();

      return $this->redirectToRoute("app_");
    }
    return $this->render("app/addOffer.html.twig", [
      "form" => $form->createView(),
    ]);
  }

  #[Route("/edit-offer/{id}", name: "edit_offer")]
  public function editOffer(EntityManagerInterface $entityManager, Request $request)
  {
    $offer = new Offer();

    $offerId = $request->query->get("id");
    if ($offerId) {
      $offer = $entityManager->getRepository(Offer::class)->find($offerId);
      if (!$offer) {
        throw $this->createNotFoundException("Offer not found");
      }
    }

    $form = $this->createForm(OfferType::class, $offer);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      return $this->redirectToRoute("app_");
    }

    return $this->render("app/editOffer.html.twig", [
      "form" => $form->createView(),
    ]);
  }

  #[Route("/delete-offer/{id}", name: "delete_offer")]
  public function deleteOffer(EntityManagerInterface $entityManager, int $id): Response
  {
    $offer = $entityManager->getRepository(Offer::class)->find($id);
    if (!$offer) {
      throw $this->createNotFoundException("Offer not found");
    }
    $entityManager->remove($offer);
    $entityManager->flush();

    return $this->redirectToRoute("app_");
  }

  #[Route("/add-candidacy/{id}", name: "add_candidacy")]
  public function addCandidacy(
    EntityManagerInterface $entityManager,
    Request $request,
    string $id
  ): Response {
    $candidacy = new Candidacy();

    $form = $this->createForm(CandidacyType::class, $candidacy);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $offer = $entityManager->getRepository(Offer::class)->find($id);
      $candidacy->setOffer($offer);
      $candidacy->addUser($this->getUser());
      $entityManager->persist($candidacy);
      $entityManager->flush();

      return $this->redirectToRoute("app_");
    }
    return $this->render("app/addOffer.html.twig", [
      "form" => $form->createView(),
    ]);
  }

  #[Route("/delete-candidacy/{id}", name: "delete_candidacy")]
  public function deleteCandidacy(EntityManagerInterface $entityManager, int $id): Response
  {
    $candidacy = $entityManager->getRepository(Candidacy::class)->find($id);
    if (!$candidacy) {
      throw $this->createNotFoundException("Candidacy not found");
    }
    $entityManager->remove($candidacy);
    $entityManager->flush();

    return $this->redirectToRoute("app_");
  }
}
