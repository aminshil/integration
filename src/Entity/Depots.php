<?php

namespace App\Entity;

use App\Repository\DepotsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepotsRepository::class)]
class Depots
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"Le Nom est Obligatoire")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s]+$/", message:"Le Nom doit Valide")]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"Il faut saisir l'adresse ")]
    #[Assert\Length(max: 50, maxMessage :" Adresse doit étre valide " )]
    private ?string $Adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"Donner l'Etat ")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s]+$/", message :" Vérifier L'Etat " )]
    private ?string $Etat = null;

    #[ORM\Column(length: 255)]
    private ?string $Image = null;


    #[ORM\ManyToOne(inversedBy: 'depot')]
    #[Assert\NotBlank (message:"Il faut choisir Categorie")]
    private ?CategorieDepot $categorie = null;

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

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): static
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $Etat): static
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): static
    {
        $this->Image = $Image;

        return $this;
    }

   
    public function getCategorie(): ?CategorieDepot
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieDepot $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }
}
