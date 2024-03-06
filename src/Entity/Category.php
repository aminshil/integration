<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeofcat = null;

    #[ORM\OneToMany(targetEntity: Demande::class, mappedBy: 'Category')]
    private Collection $Demande;

    public function __construct()
    {
        $this->Demande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeofcat(): ?string
    {
        return $this->typeofcat;
    }

    public function setTypeofcat(string $typeofcat): static
    {
        $this->typeofcat = $typeofcat;

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemande(): Collection
    {
        return $this->Demande;
    }

    public function addDemande(Demande $demande): static
    {
        if (!$this->Demande->contains($demande)) {
            $this->Demande->add($demande);
            $demande->setCategory($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): static
    {
        if ($this->Demande->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getCategory() === $this) {
                $demande->setCategory(null);
            }
        }

        return $this;
    }
}
