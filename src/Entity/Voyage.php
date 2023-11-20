<?php

namespace App\Entity;

use App\Repository\VoyageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: VoyageRepository::class)]
class Voyage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[NotBlank]
    private ?Planet $planet = null;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    #[Length(min: 3, max: 255)]
    private ?string $purpose = null;

    #[ORM\Column]
    private bool $wormholeUpgrade = false;

    #[ORM\Column]
    #[NotBlank]
    private ?\DateTimeImmutable $leaveAt;

    public function __construct()
    {
        $this->leaveAt = new \DateTimeImmutable('+1 month');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanet(): ?Planet
    {
        return $this->planet;
    }

    public function setPlanet(?Planet $planet): static
    {
        $this->planet = $planet;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): static
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getLeaveAt(): ?\DateTimeImmutable
    {
        return $this->leaveAt;
    }

    public function setLeaveAt(?\DateTimeImmutable $leaveAt): static
    {
        $this->leaveAt = $leaveAt;

        return $this;
    }

    public function getWormholeUpgrade(): bool
    {
        return $this->wormholeUpgrade;
    }

    public function setWormholeUpgrade(?bool $wormholeUpgrade): self
    {
        $this->wormholeUpgrade = (bool) $wormholeUpgrade;

        return $this;
    }
}
