<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message:"La publication ne doit pas étre vide")]
    #[Assert\Length(min:5, minMessage:"La publication doit contient au minimum {{ limit }} characters")]
    private ?string $publication = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateofpub = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Sujet de la publication ne doit pas étre vide")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Le sujet de la publication ne doit pas contenir de chiffres.'
    )]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le sujet de la publication ne doit pas etre plus long que  {{ limit }} characters"
    )]
    private ?string $topicofpub = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'Publication', cascade: ['remove'])]
    private Collection $comments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagepub = null;

    #[ORM\ManyToOne(inversedBy: 'publicationuser')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $publicationuser = null;

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


    public function getPublication(): ?string
    {
        return $this->publication;
    }

    public function setPublication(string $publication): static
    {
        $this->publication = $publication;

        return $this;
    }

    public function getDateofpub(): ?\DateTimeInterface
    {
        return $this->dateofpub;
    }

    public function setDateofpub(\DateTimeInterface $dateofpub): static
    {
        $this->dateofpub = $dateofpub;

        return $this;
    }

    public function getTopicofpub(): ?string
    {
        return $this->topicofpub;
    }

    public function setTopicofpub(string $topicofpub): static
    {
        $this->topicofpub = $topicofpub;

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
            $comment->setPublication($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPublication() === $this) {
                $comment->setPublication(null);
            }
        }

        return $this;
    }

    public function getImagepub(): ?string
    {
        return $this->imagepub;
    }

    public function setImagepub(?string $imagepub): static
    {
        $this->imagepub = $imagepub;

        return $this;
    }

    public function getPublicationuser(): ?User
    {
        return $this->publicationuser;
    }

    public function setPublicationuser(?User $publicationuser): static
    {
        $this->publicationuser = $publicationuser;

        return $this;
    }
}
