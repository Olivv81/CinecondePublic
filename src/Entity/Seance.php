<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeanceRepository::class)
 */
class Seance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $vo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $projection;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $soustitre;

    /**
     * @ORM\ManyToOne(targetEntity=Film::class, inversedBy="seances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $film;

    /**
     * @ORM\OneToMany(targetEntity=Horaire::class, mappedBy="seance", orphanRemoval=true)
     */
    private $horaires;

    public function __construct()
    {
        $this->horaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVo(): ?string
    {
        return $this->vo;
    }

    public function setVo(?string $vo): self
    {
        $this->vo = $vo;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getProjection(): ?string
    {
        return $this->projection;
    }

    public function setProjection(?string $projection): self
    {
        $this->projection = $projection;

        return $this;
    }

    public function getSoustitre(): ?string
    {
        return $this->soustitre;
    }

    public function setSoustitre(?string $soustitre): self
    {
        $this->soustitre = $soustitre;

        return $this;
    }

    public function getFilm(): ?film
    {
        return $this->film;
    }

    public function setFilm(?film $film): self
    {
        $this->film = $film;

        return $this;
    }

    /**
     * @return Collection|Horaire[]
     */
    public function getHoraires(): Collection
    {
        return $this->horaires;
    }

    public function addHoraire(Horaire $horaire): self
    {
        if (!$this->horaires->contains($horaire)) {
            $this->horaires[] = $horaire;
            $horaire->setSeance($this);
        }

        return $this;
    }

    public function removeHoraire(Horaire $horaire): self
    {
        if ($this->horaires->removeElement($horaire)) {
            // set the owning side to null (unless already changed)
            if ($horaire->getSeance() === $this) {
                $horaire->setSeance(null);
            }
        }

        return $this;
    }
}