<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inscriptionAccueil;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $desincriptionAccueil;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inscriptionProjection;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $desinscriptionProjection;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInscriptionAccueil(): ?bool
    {
        return $this->inscriptionAccueil;
    }

    public function setInscriptionAccueil(?bool $inscriptionAccueil): self
    {
        $this->inscriptionAccueil = $inscriptionAccueil;

        return $this;
    }

    public function getDesincriptionAccueil(): ?bool
    {
        return $this->desincriptionAccueil;
    }

    public function setDesincriptionAccueil(?bool $desincriptionAccueil): self
    {
        $this->desincriptionAccueil = $desincriptionAccueil;

        return $this;
    }

    public function getInscriptionProjection(): ?bool
    {
        return $this->inscriptionProjection;
    }

    public function setInscriptionProjection(?bool $inscriptionProjection): self
    {
        $this->inscriptionProjection = $inscriptionProjection;

        return $this;
    }

    public function getDesinscriptionProjection(): ?bool
    {
        return $this->desinscriptionProjection;
    }

    public function setDesinscriptionProjection(?bool $desinscriptionProjection): self
    {
        $this->desinscriptionProjection = $desinscriptionProjection;

        return $this;
    }
}
