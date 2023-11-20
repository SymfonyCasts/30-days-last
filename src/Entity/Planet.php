<?php

namespace App\Entity;

use App\Repository\PlanetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: PlanetRepository::class)]
class Planet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[NotBlank]
    #[GreaterThanOrEqual(0)]
    private ?float $lightYearsFromEarth = null;

    #[ORM\Column()]
    #[NotBlank]
    private ?string $imageFilename = null;

    #[ORM\Column]
    private bool $isInMilkyWay = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getLightYearsFromEarth(): ?float
    {
        return $this->lightYearsFromEarth;
    }

    public function setLightYearsFromEarth(?float $lightYearsFromEarth): static
    {
        $this->lightYearsFromEarth = $lightYearsFromEarth;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function isInMilkyWay(): bool
    {
        return $this->isInMilkyWay;
    }

    public function setIsInMilkyWay(bool $isInMilkyWay): self
    {
        $this->isInMilkyWay = $isInMilkyWay;

        return $this;
    }
}
