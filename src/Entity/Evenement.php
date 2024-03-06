<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\EntityManagerInterface;


#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[UniqueEntity(fields: ["nom"], message: "Ce nom d'événement est déjà utilisé.")]
class Evenement
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]    
    #[Assert\NotBlank(message: "Le nom est requis")]
    #[Assert\Length(min:3, minMessage:"Le nom doit contient au minimum {{ limit }} caractères.")]
    #[Assert\Length(max:15, maxMessage:"Le nom doit contient au maximum {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-zA-Z])[a-zA-Z\d ]+$/",
        message: "Le nom de l'événement doit contenir au moins une lettre, pas des chiffres seulement et pas des caractères speciaux"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:20, minMessage:"La description doit contient au minimum {{ limit }} caractères.")]
    #[Assert\NotBlank(message: "La description est requise")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est requise")]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être égale ou supérieure à aujourd'hui")]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est requise")]
    #[Assert\GreaterThan(propertyPath: "date_debut", message: "La date de fin doit être supérieure à la date de début")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:10, minMessage:"L'objectif doit contient au minimum {{ limit }} caractères.")]
    #[Assert\NotBlank(message: "L'objectif est requis")]
    private ?string $objectif = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Choisir oui ou non")]
    private ?bool $formation = null;


    #[ORM\Column(length: 600)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Map est requise")]
    private ?float $longitude = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse en text est requise")]
    private ?string $locationtext = null;

    #[ORM\ManyToOne(inversedBy: 'userevenement')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_evenement = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'participationevenement')]
    private Collection $participationevenement;

   
    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->participationevenement = new ArrayCollection();

    }

    
    public function updateEtat(): void
    {
        $currentDate = new \DateTime();
        if ($this->getEtat() != 'supprimé') {
            if ($currentDate > $this->getDateFin()) {
                $this->etat = 'finis';
            } elseif ($currentDate > $this->getDateDebut() && $currentDate < $this->getDateFin()) {
                $this->etat = 'en cours';
            } elseif ($currentDate < $this->getDateDebut()) {
                $this->etat = 'à venir';
            }
        }
    }
    


    public function calculatePeriod(): string
    {
        $interval = $this->date_debut->diff($this->date_fin);
    
        $period = '';
        if ($interval->d > 0) {
            $period .= $interval->d == 1 ? '1 jour' : $interval->d . ' jours';
        }
        if ($interval->h > 0) {
            $period .= ($period ? ', ' : '') . ($interval->h == 1 ? '1 heure' : $interval->h . ' heures');
        }
        if ($interval->i > 0) {
            $period .= ($period ? ', ' : '') . ($interval->i == 1 ? '1 minute' : $interval->i . ' minutes');
        }
        return $period;
    }

    public function calculatePeriodeTemp(): int
    {
        if ($this->date_debut !== null && $this->date_fin !== null) {
            $interval = $this->date_debut->diff($this->date_fin);
            $totalSeconds = $interval->s + $interval->i * 60 + $interval->h * 3600 + $interval->d * 86400 + $interval->m * 2629746 + $interval->y * 31556952;
            return (int)$totalSeconds;
        } else {
            throw new \Exception('Dates are not set.');
        }
    }




    public function getNumberOfComments(): int
{
    return $this->commentaires->count();
}


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom   = null ): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description   = null): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif   = null): static
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function isFormation(): ?bool
    {
        return $this->formation;
    }

    public function setFormation(bool $formation): static
    {
        $this->formation = $formation;

        return $this;
    }




    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image = null ): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setEvenement($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getEvenement() === $this) {
                $commentaire->setEvenement(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location ): static
    {
        $this->location = $location;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getLocationtext(): ?string
    {
        return $this->locationtext;
    }

    public function setLocationtext(string $locationtext = null ): static
    {
        $this->locationtext = $locationtext;

        return $this;
    }




    public function getUserEvenement(): ?User
    {
        return $this->user_evenement;
    }

    public function setUserEvenement(?User $user_evenement): static
    {
        $this->user_evenement = $user_evenement;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipationevenement(): Collection
    {
        return $this->participationevenement;
    }

    public function addParticipationevenement(User $participationevenement): static
    {
        if (!$this->participationevenement->contains($participationevenement)) {
            $this->participationevenement->add($participationevenement);
        }

        return $this;
    }

    public function removeParticipationevenement(User $participationevenement): static
    {
        $this->participationevenement->removeElement($participationevenement);

        return $this;
    }
}
