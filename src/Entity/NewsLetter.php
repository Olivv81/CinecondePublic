<?php

namespace App\Entity;

use App\Repository\NewsLetterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsLetterRepository::class)
 */
class NewsLetter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $event;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $films;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $docs;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $test;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailTest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\ManyToMany(targetEntity=ContactNL::class, inversedBy="newsLetters")
     */
    private $destinataires;

    public function __construct()
    {
        $this->destinataires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }


    public function isEvent(): ?bool
    {
        return $this->event;
    }

    public function setEvent(?bool $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function isFilms(): ?bool
    {
        return $this->films;
    }

    public function setFilms(?bool $films): self
    {
        $this->films = $films;

        return $this;
    }

    public function isDocs(): ?bool
    {
        return $this->docs;
    }

    public function setDocs(?bool $docs): self
    {
        $this->docs = $docs;

        return $this;
    }

    public function isTest(): ?bool
    {
        return $this->test;
    }

    public function setTest(?bool $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getEmailTest(): ?string
    {
        return $this->emailTest;
    }

    public function setEmailTest(string $emailTest): self
    {
        $this->emailTest = $emailTest;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection<int, ContactNL>
     */
    public function getDestinataires(): Collection
    {
        return $this->destinataires;
    }

    public function addDestinataire(ContactNL $destinataire): self
    {
        if (!$this->destinataires->contains($destinataire)) {
            $this->destinataires[] = $destinataire;
        }

        return $this;
    }

    public function removeDestinataire(ContactNL $destinataire): self
    {
        $this->destinataires->removeElement($destinataire);

        return $this;
    }
}
