<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: '`log`')]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Employe $employe = null;

    #[ORM\Column(length: 100)]
    private ?string $action = null;

    #[ORM\Column(length: 100)]
    private ?string $tableConcernee = null;

    #[ORM\Column(nullable: true)]
    private ?int $idObjet = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ancienEtat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $nouvelEtat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAction = null;

    public function __construct()
    {
        $this->dateAction = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): static
    {
        $this->employe = $employe;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getTableConcernee(): ?string
    {
        return $this->tableConcernee;
    }

    public function setTableConcernee(string $tableConcernee): static
    {
        $this->tableConcernee = $tableConcernee;

        return $this;
    }

    public function getIdObjet(): ?int
    {
        return $this->idObjet;
    }

    public function setIdObjet(?int $idObjet): static
    {
        $this->idObjet = $idObjet;

        return $this;
    }

    public function getAncienEtat(): ?string
    {
        return $this->ancienEtat;
    }

    public function setAncienEtat(?string $ancienEtat): static
    {
        $this->ancienEtat = $ancienEtat;

        return $this;
    }

    public function getNouvelEtat(): ?string
    {
        return $this->nouvelEtat;
    }

    public function setNouvelEtat(?string $nouvelEtat): static
    {
        $this->nouvelEtat = $nouvelEtat;

        return $this;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->dateAction;
    }

    public function setDateAction(\DateTimeInterface $dateAction): static
    {
        $this->dateAction = $dateAction;

        return $this;
    }
}
