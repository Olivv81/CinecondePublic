<?php

namespace App\Entity;

use App\Repository\ContactNLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContactNLRepository::class)
 * @UniqueEntity(
 *      fields="eMail",
 *      message="Cet adresse mail est déjà enregistrée..."
 * )
 */

class ContactNL
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email
     */
    private $eMail;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity=NewsLetter::class, mappedBy="destinataires")
     */
    private $newsLetters;

    public function __construct()
    {
        $this->newsLetters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEMail(): ?string
    {
        return $this->eMail;
    }

    public function setEMail(string $eMail): self
    {
        $this->eMail = $eMail;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, NewsLetter>
     */
    public function getNewsLetters(): Collection
    {
        return $this->newsLetters;
    }

    public function addNewsLetter(NewsLetter $newsLetter): self
    {
        if (!$this->newsLetters->contains($newsLetter)) {
            $this->newsLetters[] = $newsLetter;
            $newsLetter->addDestinataire($this);
        }

        return $this;
    }

    public function removeNewsLetter(NewsLetter $newsLetter): self
    {
        if ($this->newsLetters->removeElement($newsLetter)) {
            $newsLetter->removeDestinataire($this);
        }

        return $this;
    }
}
