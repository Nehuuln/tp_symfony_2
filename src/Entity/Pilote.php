<?php

namespace App\Entity;

use App\Repository\PiloteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PiloteRepository::class)]
class Pilote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $licencePoints = null;

    #[ORM\Column]
    private ?\DateTime $startedAt = null;

    #[ORM\Column]
    private ?bool $isTitulaire = null;

    #[ORM\ManyToOne(inversedBy: 'pilotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ecurie $ecurie = null;

    /**
     * @var Collection<int, Infraction>
     */
    #[ORM\OneToMany(targetEntity: Infraction::class, mappedBy: 'driver')]
    private Collection $infractions;

    public function __construct()
    {
        $this->infractions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getLicencePoints(): ?int
    {
        return $this->licencePoints;
    }

    public function setLicencePoints(int $licencePoints): static
    {
        $this->licencePoints = $licencePoints;

        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTime $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function isTitulaire(): ?bool
    {
        return $this->isTitulaire;
    }

    public function setIsTitulaire(bool $isTitulaire): static
    {
        $this->isTitulaire = $isTitulaire;

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

    /**
     * @return Collection<int, Infraction>
     */
    public function getInfractions(): Collection
    {
        return $this->infractions;
    }

    public function addInfraction(Infraction $infraction): static
    {
        if (!$this->infractions->contains($infraction)) {
            $this->infractions->add($infraction);
            $infraction->setPilote($this);
        }

        return $this;
    }

    public function removeInfraction(Infraction $infraction): static
    {
        if ($this->infractions->removeElement($infraction)) {
            // set the owning side to null (unless already changed)
            if ($infraction->getPilote() === $this) {
                $infraction->setPilote(null);
            }
        }

        return $this;
    }
}
