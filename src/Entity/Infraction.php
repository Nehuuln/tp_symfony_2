<?php

namespace App\Entity;

use App\Repository\InfractionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfractionRepository::class)]
class Infraction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raceName = null;

    #[ORM\Column]
    private ?\DateTime $occuredAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $pointsPenalty = null;

    #[ORM\ManyToOne(inversedBy: 'infractions')]
    private ?Pilote $driver = null;

    #[ORM\ManyToOne(inversedBy: 'infractions')]
    private ?Ecurie $ecurie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaceName(): ?string
    {
        return $this->raceName;
    }

    public function setRaceName(string $raceName): static
    {
        $this->raceName = $raceName;

        return $this;
    }

    public function getOccuredAt(): ?\DateTime
    {
        return $this->occuredAt;
    }

    public function setOccuredAt(\DateTime $occuredAt): static
    {
        $this->occuredAt = $occuredAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPointsPenalty(): ?int
    {
        return $this->pointsPenalty;
    }

    public function setPointsPenalty(int $pointsPenalty): static
    {
        $this->pointsPenalty = $pointsPenalty;

        return $this;
    }

    public function getPilote(): ?Pilote
    {
        return $this->driver;
    }

    public function setPilote(?Pilote $pilote): static
    {
        $this->driver = $pilote;

        return $this;
    }

    public function getEcurie(): ?Ecurie
    {
        return $this->ecurie;
    }

    public function setEcurie(?Ecurie $ecurie): static
    {
        $this->ecurie = $ecurie;

        return $this;
    }
}
