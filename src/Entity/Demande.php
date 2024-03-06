<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\BadWordFilter;


#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text', options: ["collation" => "utf8mb4_unicode_ci"])]
    #[Assert\NotBlank(message:"Demande ne doit pas étre vide")]
    #[Assert\Length(min:5, minMessage:"La demande doit avoir au minimum {{ limit }} characters")]
    private ?string $demande = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Nom de l'object ne doit pas étre vide")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Le nom de l\'objet ne doit pas contenir de chiffres.'
    )]
    #[Assert\Length(
        max: 20,
        maxMessage: "Nom de l'objet ne doit pas etre plus long que  {{ limit }} characters"
    )]
    private ?string $nameofobj = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Urgence de la demande  ne doit pas étre vide")]
    #[Assert\Choice(choices: ['urgente', 'normale', 'faible'], message: 'choisir une urgence valide.')]
    private ?string $stateofdem = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
   private ?\DateTimeInterface $dateofdem = null;

    #[ORM\ManyToOne(inversedBy: 'Demande')]
    #[ORM\JoinColumn(nullable: false)]
    
    private ?Category $Category = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'Demande', cascade: ['remove'])]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'demandeuser')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $demandeuser = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }
    public function getNumberOfComments(): int
    {
        return $this->comments->count();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getDemande(): ?string
    {
        return $this->demande;
    }

    public function setDemande(string $demande): static
    {
        $this->demande = $demande;

        return $this;
    }

    public function getNameofobj(): ?string
    {
        return $this->nameofobj;
    }

    public function setNameofobj(string $nameofobj): static
    {
        $this->nameofobj = $nameofobj;

        return $this;
    }

    public function getStateofdem(): ?string
    {
        return $this->stateofdem;
    }

    public function setStateofdem(string $stateofdem): static
    {
        $this->stateofdem = $stateofdem;

        return $this;
    }

    public function getDateofdem(): ?\DateTimeInterface
    {
        return $this->dateofdem;
    }

    public function setDateofdem(\DateTimeInterface $dateofdem): static
    {
        $this->dateofdem = $dateofdem;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDemande($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDemande() === $this) {
                $comment->setDemande(null);
            }
        }

        return $this;
    }

    public function getDemandeuser(): ?User
    {
        return $this->demandeuser;
    }

    public function setDemandeuser(?User $demandeuser): static
    {
        $this->demandeuser = $demandeuser;

        return $this;
    }



    
}
