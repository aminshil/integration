<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu de commentaire est requis")]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenement $evenement = null;

    #[ORM\ManyToOne(inversedBy: 'commentaireuser')]
    private ?User $commentaireuser = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu  = null): static
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

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    public function getCommentaireuser(): ?User
    {
        return $this->commentaireuser;
    }

    public function setCommentaireuser(?User $commentaireuser): static
    {
        $this->commentaireuser = $commentaireuser;

        return $this;
    }
}
