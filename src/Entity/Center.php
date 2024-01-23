<?php

namespace App\Entity;

use App\Repository\CenterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CenterRepository::class)]
class Center
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $location_center = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocationCenter(): ?string
    {
        return $this->location_center;
    }

    public function setLocationCenter(string $location_center): static
    {
        $this->location_center = $location_center;

        return $this;
    }
}
