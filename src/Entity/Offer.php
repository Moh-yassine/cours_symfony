<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(length: 255)]
  private ?string $city = null;

  #[ORM\Column(length: 255)]
  private ?string $tag = null;

  #[ORM\ManyToOne(inversedBy: 'offers')]
  private ?User $recruiter = null;

  /**
   * @var Collection<int, Candidacy>
   */
  #[ORM\OneToMany(targetEntity: Candidacy::class, mappedBy: 'offer')]
  private Collection $candidacy;

  #[ORM\Column]
  #[Gedmo\Timestampable]
  private ?\DateTime $created_at = null;

  #[ORM\Column]
  #[Gedmo\Timestampable]
  private ?\DateTime $update_at = null;

  public function __construct()
  {
      $this->candidacy = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): static
  {
    $this->description = $description;

    return $this;
  }

  public function getcity(): ?string
  {
    return $this->city;
  }

  public function setcity(string $city): static
  {
    $this->city = $city;

    return $this;
  }

  public function getTag(): ?string
  {
    return $this->tag;
  }

  public function setTag(string $tag): static
  {
    $this->tag = $tag;

    return $this;
  }

  public function getRecruiter(): ?User
  {
      return $this->recruiter;
  }

  public function setRecruiter(?User $recruiter): static
  {
      $this->recruiter = $recruiter;

      return $this;
  }

  /**
   * @return Collection<int, Candidacy>
   */
  public function getCandidacy(): Collection
  {
      return $this->candidacy;
  }

  public function addCandidacy(Candidacy $candidacy): static
  {
      if (!$this->candidacy->contains($candidacy)) {
          $this->candidacy->add($candidacy);
          $candidacy->setOffer($this);
      }

      return $this;
  }

  public function removeCandidacy(Candidacy $candidacy): static
  {
      if ($this->candidacy->removeElement($candidacy)) {
          // set the owning side to null (unless already changed)
          if ($candidacy->getOffer() === $this) {
              $candidacy->setOffer(null);
          }
      }

      return $this;
  }

  public function getCreatedAt(): ?\DateTime
  {
      return $this->created_at;
  }

  public function setCreatedAt(\DateTime $created_at): static
  {
      $this->created_at = $created_at;

      return $this;
  }

  public function getUpdateAt(): ?\DateTime
  {
      return $this->update_at;
  }

  public function setUpdateAt(\DateTime $update_at): static
  {
      $this->update_at = $update_at;

      return $this;
  }
}
