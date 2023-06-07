<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="array", nullable=true)
     */
    private $accueil = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $Projection = [];

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":"true"})
     */
    private $aLAffiche;

    /**
     * @ORM\Column(type="string", length=255, nullable=true )
     */
    private $typeSeance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IdEMS;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $TroisD;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $VO;

    /**
     * @ORM\ManyToOne(targetEntity=Film::class, inversedBy="horaires")
     */
    private $Film;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inscriptionAccueil;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inscriptionProjection;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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


    public function getAccueil(): ?array
    {
        return $this->accueil;
    }

    public function setAccueil(?array $accueil): self
    {
        $this->accueil = $accueil;

        return $this;
    }

    public function getProjection(): ?array
    {
        return $this->Projection;
    }

    public function setProjection(?array $Projection): self
    {
        $this->Projection = $Projection;

        return $this;
    }

    public function getALAffiche(): ?bool
    {
        return $this->aLAffiche;
    }

    public function setALAffiche(?bool $aLAffiche): self
    {
        $this->aLAffiche = $aLAffiche;

        return $this;
    }

    public function getTypeSeance(): ?string
    {
        return $this->typeSeance;
    }

    public function setTypeSeance(?string $typeSeance): self
    {
        $this->typeSeance = $typeSeance;

        return $this;
    }

    public function getIdEMS(): ?string
    {
        return $this->IdEMS;
    }

    public function setIdEMS(?string $IdEMS): self
    {
        $this->IdEMS = $IdEMS;

        return $this;
    }

    public function isTroisD(): ?bool
    {
        return $this->TroisD;
    }

    public function setTroisD(?bool $TroisD): self
    {
        $this->TroisD = $TroisD;

        return $this;
    }

    public function isVO(): ?bool
    {
        return $this->VO;
    }

    public function setVO(?bool $VO): self
    {
        $this->VO = $VO;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->Film;
    }

    public function setFilm(?Film $Film): self
    {
        $this->Film = $Film;

        return $this;
    }

    public function isInscriptionAccueil(): ?bool
    {
        return $this->inscriptionAccueil;
    }

    public function setInscriptionAccueil(?bool $inscriptionAccueil): self
    {
        $this->inscriptionAccueil = $inscriptionAccueil;

        return $this;
    }

    public function isInscriptionProjection(): ?bool
    {
        return $this->inscriptionProjection;
    }

    public function setInscriptionProjection(?bool $inscriptionProjection): self
    {
        $this->inscriptionProjection = $inscriptionProjection;

        return $this;
    }
}
