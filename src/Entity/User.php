<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: "Invalid email address")]
    #[Assert\NotBlank(message: "email is required")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Length(min: 3, max: 255, maxMessage: "First name cannot exceed {{ limit }} characters")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "name is required")]
    #[Assert\Regex(
        pattern: "/^[A-Za-z]+$/",
        message: "Last name can only contain letters"
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "prenom is required")]
    #[Assert\Regex(
        pattern: "/^[A-Za-z]+$/",
        message: "le prenom can only contain letters"
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Date of birth is required")]
    private ?\DateTimeInterface $dateDeNaissance = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column]
    private $isBlocked = false;

    #[ORM\OneToMany(mappedBy: 'user_evenement', targetEntity: Evenement::class, orphanRemoval: true)]
    private Collection $userevenement;

    #[ORM\ManyToMany(targetEntity: Evenement::class, mappedBy: 'participationevenement')]
    private Collection $participationevenement;

    #[ORM\OneToMany(mappedBy: 'commentaireuser', targetEntity: Commentaire::class)]
    private Collection $commentaireuser;

    #[ORM\OneToMany(mappedBy: 'commentaireuserformation', targetEntity: Commentaireformation::class ,cascade: ['remove'])]
    private Collection $commentaireformationuser;

    #[ORM\OneToMany(mappedBy: 'formationuser', targetEntity: Formation::class, cascade: ['remove'])]
    private Collection $formationuser;

    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'participation')]
    private Collection $participationformation;

    #[ORM\OneToMany(mappedBy: 'demandeuser', targetEntity: Demande::class, orphanRemoval: true)]
    private Collection $demandeuser;

    #[ORM\OneToMany(mappedBy: 'publicationuser', targetEntity: Publication::class, orphanRemoval: true)]
    private Collection $publicationuser;

    #[ORM\OneToMany(mappedBy: 'commentpubdemuser', targetEntity: Comment::class)]
    private Collection $commentpubdem;

    public function __construct()
    {
        $this->userevenement = new ArrayCollection();
        $this->participationevenement = new ArrayCollection();
        $this->commentaireuser = new ArrayCollection();
        $this->commentaireformationuser = new ArrayCollection();
        $this->formationuser = new ArrayCollection();
        $this->participationformation = new ArrayCollection();
        $this->demandeuser = new ArrayCollection();
        $this->publicationuser = new ArrayCollection();
        $this->commentpubdem = new ArrayCollection();
    }

   


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];

        // guarantee every user at least has ROLE_USER

        if ($this->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isSociete()) {
            $roles[] = 'ROLE_SOCIETE';
        }
        if ($this->isUser()) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }
    public function isSociete(): bool
        {
            return $this->hasRole('ROLE_SOCIETE');
        }
        public function isUser(): bool
        {
            return $this->hasRole('ROLE_USER');
        }
    public function is(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }
    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): static
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): static
{
    $this->isBlocked = $isBlocked;
    return $this;
}

    /**
     * @return Collection<int, Evenement>
     */
    public function getUserevenement(): Collection
    {
        return $this->userevenement;
    }

    public function addUserevenement(Evenement $userevenement): static
    {
        if (!$this->userevenement->contains($userevenement)) {
            $this->userevenement->add($userevenement);
            $userevenement->setUserEvenement($this);
        }

        return $this;
    }

    public function removeUserevenement(Evenement $userevenement): static
    {
        if ($this->userevenement->removeElement($userevenement)) {
            // set the owning side to null (unless already changed)
            if ($userevenement->getUserEvenement() === $this) {
                $userevenement->setUserEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getParticipationevenement(): Collection
    {
        return $this->participationevenement;
    }

    public function addParticipationevenement(Evenement $participationevenement): static
    {
        if (!$this->participationevenement->contains($participationevenement)) {
            $this->participationevenement->add($participationevenement);
            $participationevenement->addParticipationevenement($this);
        }

        return $this;
    }

    public function removeParticipationevenement(Evenement $participationevenement): static
    {
        if ($this->participationevenement->removeElement($participationevenement)) {
            $participationevenement->removeParticipationevenement($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaireuser(): Collection
    {
        return $this->commentaireuser;
    }

    public function addCommentaireuser(Commentaire $commentaireuser): static
    {
        if (!$this->commentaireuser->contains($commentaireuser)) {
            $this->commentaireuser->add($commentaireuser);
            $commentaireuser->setCommentaireuser($this);
        }

        return $this;
    }

    public function removeCommentaireuser(Commentaire $commentaireuser): static
    {
        if ($this->commentaireuser->removeElement($commentaireuser)) {
            // set the owning side to null (unless already changed)
            if ($commentaireuser->getCommentaireuser() === $this) {
                $commentaireuser->setCommentaireuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaireformation>
     */
    public function getCommentaireformationuser(): Collection
    {
        return $this->commentaireformationuser;
    }

    public function addCommentaireformationuser(Commentaireformation $commentaireformationuser): static
    {
        if (!$this->commentaireformationuser->contains($commentaireformationuser)) {
            $this->commentaireformationuser->add($commentaireformationuser);
            $commentaireformationuser->setCommentaireuserformation($this);
        }

        return $this;
    }

    public function removeCommentaireformationuser(Commentaireformation $commentaireformationuser): static
    {
        if ($this->commentaireformationuser->removeElement($commentaireformationuser)) {
            // set the owning side to null (unless already changed)
            if ($commentaireformationuser->getCommentaireuserformation() === $this) {
                $commentaireformationuser->setCommentaireuserformation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormationuser(): Collection
    {
        return $this->formationuser;
    }

    public function addFormationuser(Formation $formationuser): static
    {
        if (!$this->formationuser->contains($formationuser)) {
            $this->formationuser->add($formationuser);
            $formationuser->setFormationuser($this);
        }

        return $this;
    }

    public function removeFormationuser(Formation $formationuser): static
    {
        if ($this->formationuser->removeElement($formationuser)) {
            // set the owning side to null (unless already changed)
            if ($formationuser->getFormationuser() === $this) {
                $formationuser->setFormationuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getParticipationformation(): Collection
    {
        return $this->participationformation;
    }

    public function addParticipationformation(Formation $participationformation): static
    {
        if (!$this->participationformation->contains($participationformation)) {
            $this->participationformation->add($participationformation);
            $participationformation->addParticipation($this);
        }

        return $this;
    }

    public function removeParticipationformation(Formation $participationformation): static
    {
        if ($this->participationformation->removeElement($participationformation)) {
            $participationformation->removeParticipation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemandeuser(): Collection
    {
        return $this->demandeuser;
    }

    public function addDemandeuser(Demande $demandeuser): static
    {
        if (!$this->demandeuser->contains($demandeuser)) {
            $this->demandeuser->add($demandeuser);
            $demandeuser->setDemandeuser($this);
        }

        return $this;
    }

    public function removeDemandeuser(Demande $demandeuser): static
    {
        if ($this->demandeuser->removeElement($demandeuser)) {
            // set the owning side to null (unless already changed)
            if ($demandeuser->getDemandeuser() === $this) {
                $demandeuser->setDemandeuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublicationuser(): Collection
    {
        return $this->publicationuser;
    }

    public function addPublicationuser(Publication $publicationuser): static
    {
        if (!$this->publicationuser->contains($publicationuser)) {
            $this->publicationuser->add($publicationuser);
            $publicationuser->setPublicationuser($this);
        }

        return $this;
    }

    public function removePublicationuser(Publication $publicationuser): static
    {
        if ($this->publicationuser->removeElement($publicationuser)) {
            // set the owning side to null (unless already changed)
            if ($publicationuser->getPublicationuser() === $this) {
                $publicationuser->setPublicationuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCommentpubdem(): Collection
    {
        return $this->commentpubdem;
    }

    public function addCommentpubdem(Comment $commentpubdem): static
    {
        if (!$this->commentpubdem->contains($commentpubdem)) {
            $this->commentpubdem->add($commentpubdem);
            $commentpubdem->setCommentpubdemuser($this);
        }

        return $this;
    }

    public function removeCommentpubdem(Comment $commentpubdem): static
    {
        if ($this->commentpubdem->removeElement($commentpubdem)) {
            // set the owning side to null (unless already changed)
            if ($commentpubdem->getCommentpubdemuser() === $this) {
                $commentpubdem->setCommentpubdemuser(null);
            }
        }

        return $this;
    }


    
    
}
