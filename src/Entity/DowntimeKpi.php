<?php

namespace App\Entity;

use App\Repository\DowntimeKpiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DowntimeKpiRepository::class)]
class DowntimeKpi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $kpi_counter = null;

    #[ORM\Column(length: 75)]
    private ?string $kpi_type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Downtime $downtime = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $kpi_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKpiCounter(): ?int
    {
        return $this->kpi_counter;
    }

    public function setKpiCounter(int $kpi_counter): static
    {
        $this->kpi_counter = $kpi_counter;

        return $this;
    }

    public function getKpiType(): ?string
    {
        return $this->kpi_type;
    }

    public function setKpiType(string $kpi_type): static
    {
        $this->kpi_type = $kpi_type;

        return $this;
    }

    public function getDowntime(): ?Downtime
    {
        return $this->downtime;
    }

    public function setDowntime(?Downtime $downtime): static
    {
        $this->downtime = $downtime;

        return $this;
    }

    public function getKpiDate(): ?\DateTimeImmutable
    {
        return $this->kpi_date;
    }

    public function setKpiDate(\DateTimeImmutable $kpi_date): static
    {
        $this->kpi_date = $kpi_date;

        return $this;
    }
}
