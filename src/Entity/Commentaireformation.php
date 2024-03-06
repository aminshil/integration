<?php

namespace App\Entity;

use App\Repository\CommentaireformationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CommentaireformationRepository::class)]
class Commentaireformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu de commentaire est requis")]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\ManyToOne(inversedBy: 'commentaireformationuser')]
    private ?User $commentaireuserformation = null;

    #[ORM\ManyToOne(inversedBy: 'commentaireformations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getCommentaireuserformation(): ?User
    {
        return $this->commentaireuserformation;
    }

    public function setCommentaireuserformation(?User $commentaireuserformation): static
    {
        $this->commentaireuserformation = $commentaireuserformation;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }
}
