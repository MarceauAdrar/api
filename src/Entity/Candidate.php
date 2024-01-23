<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dob_candidate = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email_candidate = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $uuid_candidate = null;

    #[ORM\Column]
    private ?bool $is_enabled_candidate = false;

    public function __construct()
    {
        $this->uuid_candidate = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDobCandidate(): ?\DateTimeImmutable
    {
        return $this->dob_candidate;
    }

    public function setDobCandidate(?\DateTimeImmutable $dob_candidate): static
    {
        $this->dob_candidate = $dob_candidate;

        return $this;
    }

    public function getEmailCandidate(): ?string
    {
        return $this->email_candidate;
    }

    public function setEmailCandidate(string $email_candidate): static
    {
        $this->email_candidate = $email_candidate;

        return $this;
    }

    public function getUuidCandidate(): ?Uuid
    {
        return $this->uuid_candidate;
    }

    public function setUuidCandidate(Uuid $uuid_candidate): static
    {
        $this->uuid_candidate = $uuid_candidate;

        return $this;
    }

    public function isIsEnabledCandidate(): ?bool
    {
        return $this->is_enabled_candidate;
    }

    public function setIsEnabledCandidate(bool $is_enabled_candidate): static
    {
        $this->is_enabled_candidate = $is_enabled_candidate;

        return $this;
    }
}
