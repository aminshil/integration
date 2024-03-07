<?php

namespace App\Entity;

use App\Repository\CategorieDepotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieDepotRepository::class)]
class CategorieDepot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"Nom est Obligatoire")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s]+$/", message:"Le Nom doit comporter seul des lettres")]
    private ?string $Nom= null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"Donner la Description ")]
    #[Assert\Length(max: 150 , maxMessage :"La Description doit comporter entre 1 et {{ limit }} caractères" )]
    private ?string $Description = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Depots::class)]
    private Collection $depot;

    public function __construct()
    {
        $this->depot = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection<int, Depots>
     */
    public function getDepot(): Collection
    {
        return $this->depot;
    }

    public function addDepot(Depots $depot): static
    {
        if (!$this->depot->contains($depot)) {
            $this->depot->add($depot);
            $depot->setCategorie($this);
        }

        return $this;
    }

    public function removeDepot(Depots $depot): static
    {
        if ($this->depot->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCategorie() === $this) {
                $depot->setCategorie(null);
            }
        }

        return $this;
    }
}
