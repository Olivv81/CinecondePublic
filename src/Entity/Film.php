<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Film
 *
 * @ORM\Table(name="film")
 * @ORM\Entity(repositoryClass="App\Repository\FilmRepository"))
 */
class Film
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255, nullable=false)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="realisateurs", type="string", length=255, nullable=false)
     */
    private $realisateurs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acteurs", type="string", length=255, nullable=true)
     */
    private $acteurs;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="anneeproduction", type="datetime", nullable=true)
     */
    private $anneeproduction;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_sortie", type="datetime", nullable=true)
     */
    private $dateSortie;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="duree", type="time", nullable=true)
     */
    private $duree;

    /**
     * @var string|null
     *
     * @ORM\Column(name="genre_principal", type="string", length=255, nullable=true)
     */
    private $genrePrincipal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nationalite", type="string", length=255, nullable=true)
     */
    private $nationalite;

    /**
     * @var string|null
     *
     * @ORM\Column(name="synopsis", type="string", length=2000, nullable=true)
     */
    private $synopsis;

    /**
     * @var string|null
     *
     * @ORM\Column(name="affichette", type="string", length=500, nullable=true)
     */
    private $affichette;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="string", length=255, nullable=false)
     */
    private $video;

    /**
     * @var int|null
     *
     * @ORM\Column(name="visa_number", type="integer", nullable=true)
     */
    private $visaNumber;

    /**
     * @ORM\OneToMany(targetEntity=Seance::class, mappedBy="film", orphanRemoval=true)
     */
    private $seances;

    /**
     * @ORM\Column(type="integer")
     */
    private $idFilm;

    /**
     * @ORM\ManyToMany(targetEntity=Evenement::class, mappedBy="films")
     */
    private $evenements;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $classification;





    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getRealisateurs(): ?string
    {
        return $this->realisateurs;
    }

    public function setRealisateurs(string $realisateurs): self
    {
        $this->realisateurs = $realisateurs;

        return $this;
    }

    public function getActeurs(): ?string
    {
        return $this->acteurs;
    }

    public function setActeurs(?string $acteurs): self
    {
        $this->acteurs = $acteurs;

        return $this;
    }

    public function getAnneeproduction(): ?\DateTimeInterface
    {
        return $this->anneeproduction;
    }

    public function setAnneeproduction(?\DateTimeInterface $anneeproduction): self
    {
        $this->anneeproduction = $anneeproduction;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(?\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(?\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getGenrePrincipal(): ?string
    {
        return $this->genrePrincipal;
    }

    public function setGenrePrincipal(?string $genrePrincipal): self
    {
        $this->genrePrincipal = $genrePrincipal;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getAffichette(): ?string
    {
        return $this->affichette;
    }

    public function setAffichette(?string $affichette): self
    {
        $this->affichette = $affichette;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getVisaNumber(): ?int
    {
        return $this->visaNumber;
    }

    public function setVisaNumber(?int $visaNumber): self
    {
        $this->visaNumber = $visaNumber;

        return $this;
    }

    /**
     * @return Collection|Seance[]
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances[] = $seance;
            $seance->setFilm($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->removeElement($seance)) {
            // set the owning side to null (unless already changed)
            if ($seance->getFilm() === $this) {
                $seance->setFilm(null);
            }
        }

        return $this;
    }

    public function getIdFilm(): ?int
    {
        return $this->idFilm;
    }

    public function setIdFilm(int $idFilm): self
    {
        $this->idFilm = $idFilm;

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->addFilm($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeFilm($this);
        }

        return $this;
    }

    public function getClassification(): ?string
    {
        return $this->classification;
    }

    public function setClassification(?string $classification): self
    {
        $this->classification = $classification;

        return $this;
    }
}
