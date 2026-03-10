<?php

namespace App\Entity;

use App\Repository\EtageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtageRepository::class)]
class Etage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numeroEtage = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nomEtage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    /** @var Collection<int, Chambre> */
    #[ORM\OneToMany(targetEntity: Chambre::class, mappedBy: 'etage', cascade: ['persist'])]
    private Collection $chambres;

    public function __construct()
    {
        $this->chambres = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroEtage(): ?int
    {
        return $this->numeroEtage;
    }

    public function setNumeroEtage(int $numeroEtage): static
    {
        $this->numeroEtage = $numeroEtage;

        return $this;
    }

    public function getNomEtage(): ?string
    {
        return $this->nomEtage;
    }

    public function setNomEtage(?string $nomEtage): static
    {
        $this->nomEtage = $nomEtage;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, Chambre>
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambre $chambre): static
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres->add($chambre);
            $chambre->setEtage($this);
        }

        return $this;
    }

    public function removeChambre(Chambre $chambre): static
    {
        if ($this->chambres->removeElement($chambre)) {
            if ($chambre->getEtage() === $this) {
                $chambre->setEtage(null);
            }
        }

        return $this;
    }
}
