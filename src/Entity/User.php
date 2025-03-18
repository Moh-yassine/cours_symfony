<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", fields: ["email"])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180)]
  private ?string $email = null;

  /**
   * @var list<string> The user roles
   */
  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  /**
   * @var Collection<int, Offer>
   */
  #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'recruiter')]
  private Collection $offers;

  /**
   * @var Collection<int, Candidacy>
   */
  #[ORM\ManyToMany(targetEntity: Candidacy::class, mappedBy: 'users')]
  private Collection $candidacies;

  #[ORM\Column]
  private bool $isVerified = false;

  public function __construct()
  {
      $this->offers = new ArrayCollection();
      $this->candidacies = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): static
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   *
   * @return list<string>
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = "ROLE_USER";

    return array_unique($roles);
  }

  /**
   * @param list<string> $roles
   */
  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  /**
   * @return Collection<int, Offer>
   */
  public function getOffers(): Collection
  {
      return $this->offers;
  }

  public function addOffer(Offer $offer): static
  {
      if (!$this->offers->contains($offer)) {
          $this->offers->add($offer);
          $offer->setRecruiter($this);
      }

      return $this;
  }

  public function removeOffer(Offer $offer): static
  {
      if ($this->offers->removeElement($offer)) {
          // set the owning side to null (unless already changed)
          if ($offer->getRecruiter() === $this) {
              $offer->setRecruiter(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Candidacy>
   */
  public function getCandidacies(): Collection
  {
      return $this->candidacies;
  }

  public function addCandidacy(Candidacy $candidacy): static
  {
      if (!$this->candidacies->contains($candidacy)) {
          $this->candidacies->add($candidacy);
          $candidacy->addUser($this);
      }

      return $this;
  }

  public function removeCandidacy(Candidacy $candidacy): static
  {
      if ($this->candidacies->removeElement($candidacy)) {
          $candidacy->removeUser($this);
      }

      return $this;
  }

  public function isVerified(): bool
  {
      return $this->isVerified;
  }

  public function setIsVerified(bool $isVerified): static
  {
      $this->isVerified = $isVerified;

      return $this;
  }
}
