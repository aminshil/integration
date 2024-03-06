<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message=" nom doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un nom au mini de 5 caracteres"
     *
     *     )
     */
    private ?string $nom_formation = null;

    #[ORM\Column(length: 800)]
    /**
     * @Assert\NotBlank(message=" description doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer une description au mini de 5 caracteres"
     *
     *     )
     */
    private ?string $description_formation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
/**
* @Assert\GreaterThan("today", message="La date de formation doit être supérieure à la date d'aujourd'hui.")
 */
private ?\DateTimeInterface $date_formation = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message=" nom doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un nom au mini de 5 caracteres"
     *
     *     )
     */
    private ?string $formateur = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message=" lieu doit etre non vide")
     * @Assert\Length(
     *      min = 3,
     *      minMessage=" Entrer un lieu au mini de 3 caracteres"
     *
     *     )
     */
    private ?string $lieu_formation = null;

    #[ORM\Column(length: 255)]

    private ?string $image_formation = null;

    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: Commentaireformation::class, orphanRemoval: true)]
    private Collection $commentaireformations;

    #[ORM\ManyToOne(inversedBy: 'formationuser')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $formationuser = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'participationformation')]
    private Collection $participation;


    public function getNumberOfComments(): int
{
    return $this->commentaireformations->count();
}



    public function getId(): ?int
    {
        return $this->id;
    }
    public function __construct()
    {
        // Initialise la date de formation avec la date d'aujourd'hui lors de la création de l'instance
        $this->date_formation = new \DateTime();
        $this->commentaireformations = new ArrayCollection();
        $this->participation = new ArrayCollection();
    }
    public function getNomFormation(): ?string
    {
        return $this->nom_formation;
    }

    public function setNomFormation(string $nom_formation): static
    {
        $this->nom_formation = $nom_formation;

        return $this;
    }

    public function getDescriptionFormation(): ?string
    {
        return $this->description_formation;
    }

    public function setDescriptionFormation(string $description_formation): static
    {
        $this->description_formation = $description_formation;

        return $this;
    }

    public function getDateFormation(): ?\DateTimeInterface
    {
        return $this->date_formation;
    }

    public function setDateFormation(\DateTimeInterface $date_formation): static
    {
        $this->date_formation = $date_formation;

        return $this;
    }

    public function getFormateur(): ?string
    {
        return $this->formateur;
    }

    public function setFormateur(string $formateur): static
    {
        $this->formateur = $formateur;

        return $this;
    }

    public function getLieuFormation(): ?string
    {
        return $this->lieu_formation;
    }

    public function setLieuFormation(string $lieu_formation): static
    {
        $this->lieu_formation = $lieu_formation;

        return $this;
    }

    public function getImageFormation(): ?string
    {
        return $this->image_formation;
    }

    public function setImageFormation(string $image_formation): static
    {
        $this->image_formation = $image_formation;

        return $this;
    }

    /**
     * @return Collection<int, Commentaireformation>
     */
    public function getCommentaireformations(): Collection
    {
        return $this->commentaireformations;
    }

    public function addCommentaireformation(Commentaireformation $commentaireformation): static
    {
        if (!$this->commentaireformations->contains($commentaireformation)) {
            $this->commentaireformations->add($commentaireformation);
            $commentaireformation->setFormation($this);
        }

        return $this;
    }

    public function removeCommentaireformation(Commentaireformation $commentaireformation): static
    {
        if ($this->commentaireformations->removeElement($commentaireformation)) {
            // set the owning side to null (unless already changed)
            if ($commentaireformation->getFormation() === $this) {
                $commentaireformation->setFormation(null);
            }
        }

        return $this;
    }

    public function getFormationuser(): ?User
    {
        return $this->formationuser;
    }

    public function setFormationuser(?User $formationuser): static
    {
        $this->formationuser = $formationuser;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipation(): Collection
    {
        return $this->participation;
    }

    public function addParticipation(User $participation): static
    {
        if (!$this->participation->contains($participation)) {
            $this->participation->add($participation);
        }

        return $this;
    }

    public function removeParticipation(User $participation): static
    {
        $this->participation->removeElement($participation);

        return $this;
    }

   
}
