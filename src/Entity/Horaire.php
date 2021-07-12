<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HoraireRepository::class)
 */
class Horaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $horaire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $agentAccueil;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $projectionniste;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $autre;

    /**
     * @ORM\ManyToOne(targetEntity=Seance::class, inversedBy="horaires")
     */
    private $seance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(?\DateTimeInterface $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getAgentAccueil(): ?string
    {
        return $this->agentAccueil;
    }

    public function setAgentAccueil(?string $agentAccueil): self
    {
        $this->agentAccueil = $agentAccueil;

        return $this;
    }

    public function getProjectionniste(): ?string
    {
        return $this->projectionniste;
    }

    public function setProjectionniste(?string $projectionniste): self
    {
        $this->projectionniste = $projectionniste;

        return $this;
    }

    public function getAutre(): ?string
    {
        return $this->autre;
    }

    public function setAutre(?string $autre): self
    {
        $this->autre = $autre;

        return $this;
    }

    public function getSeance(): ?seance
    {
        return $this->seance;
    }

    public function setSeance(?seance $seance): self
    {
        $this->seance = $seance;

        return $this;
    }
}
