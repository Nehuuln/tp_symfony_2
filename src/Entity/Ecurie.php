<?php

namespace App\Entity;

use App\Repository\EcurieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcurieRepository::class)]
class Ecurie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Moteur $moteur = null;

    /**
     * @var Collection<int, Pilote>
     */
    #[ORM\OneToMany(targetEntity: Pilote::class, mappedBy: 'ecurie')]
    private Collection $pilotes;

    /**
     * @var Collection<int, Infraction>
     */
    #[ORM\OneToMany(targetEntity: Infraction::class, mappedBy: 'ecurie')]
    private Collection $infractions;

    public function __construct()
    {
        $this->pilotes = new ArrayCollection();
        $this->infractions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMoteur(): ?Moteur
    {
        return $this->moteur;
    }

    public function setMoteur(Moteur $moteur): static
    {
        $this->moteur = $moteur;

        return $this;
    }

    /**
     * @return Collection<int, Pilote>
     */
    public function getPilotes(): Collection
    {
        return $this->pilotes;
    }

    public function addPilote(Pilote $pilote): static
    {
        if (!$this->pilotes->contains($pilote)) {
            $this->pilotes->add($pilote);
            $pilote->setEcurie($this);
        }

        return $this;
    }

    public function removePilote(Pilote $pilote): static
    {
        if ($this->pilotes->removeElement($pilote)) {
            // set the owning side to null (unless already changed)
            if ($pilote->getEcurie() === $this) {
                $pilote->setEcurie(null);
            }
        }

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
            $infraction->setEcurie($this);
        }

        return $this;
    }

    public function removeInfraction(Infraction $infraction): static
    {
        if ($this->infractions->removeElement($infraction)) {
            // set the owning side to null (unless already changed)
            if ($infraction->getEcurie() === $this) {
                $infraction->setEcurie(null);
            }
        }

        return $this;
    }
}
