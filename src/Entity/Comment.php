<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateofcom = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Publication $Publication = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Demande $Demande = null;

    #[ORM\ManyToOne(inversedBy: 'commentpubdem')]
    private ?User $commentpubdemuser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDateofcom(): ?\DateTimeInterface
    {
        return $this->dateofcom;
    }

    public function setDateofcom(\DateTimeInterface $dateofcom): static
    {
        $this->dateofcom = $dateofcom;

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->Publication;
    }

    public function setPublication(?Publication $Publication): static
    {
        $this->Publication = $Publication;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->Demande;
    }

    public function setDemande(?Demande $Demande): static
    {
        $this->Demande = $Demande;

        return $this;
    }

    public function getCommentpubdemuser(): ?User
    {
        return $this->commentpubdemuser;
    }

    public function setCommentpubdemuser(?User $commentpubdemuser): static
    {
        $this->commentpubdemuser = $commentpubdemuser;

        return $this;
    }
}
