<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AdherentRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/adherents",
 *             "security"="is_granted('ROLE_MANAGER')",
 *             "security_message"="Seuls les managers peuvent voir la liste des adhérents"
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/adherents",
 *             "security"="is_granted('ROLE_MANAGER')",
 *             "security_message"="Seuls les managers peuvent créer des adhérents",
 *             "denormalization_context"={
 *                 "groups"={"post_manager"}
 *             },
 *             "controller"="App\Controller\AdherentController::createAdherent"
 *         },
 *         "statNbPretsParAdherent"={
 *             "method"="GET",
 *             "route_name"="adherent_nbPrets",
 *             "path"="/adherents/nbPretsParAdherent",
 *             "controller"=StatsController::class
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/adherents/{id}",
 *             "controller"="App\Controller\AdherentController::getAdherent"
 *         },
 *         "getNbPrets"={
 *             "method"="GET",
 *             "route_name"="adherent_prets_count",
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/adherents/{id}",
 *             "controller"="App\Controller\AdherentController::updateAdherent"
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/adherents/{id}",
 *             "security"="is_granted('ROLE_ADMIN')",
 *             "security_message"="Seuls les administrateurs peuvent supprimer un adhérent"
 *         }
 *     }
 * )
 * @UniqueEntity(
 *    fields={"mail"},
 *    message="Ce mail existe déjà")
 */
class Adherent implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_ADHERENT = 'ROLE_ADHERENT';
    const DEFAULT_ROLE = 'ROLE_ADHERENT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_role_manager", "get_role_admin"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin", "post_manager"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin", "post_manager"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin", "post_manager"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin", "post_manager"})
     */
    private $codeCommune;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin", "post_manager"})
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin", "post_manager"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_admin", "post_manager"})
     */
    private $password;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"get_role_manager", "get_role_admin"})
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=Pret::class, mappedBy="adherent")
     * @ApiSubresource
     */
    private $prets;

    public function __construct()
    {
        $this->prets = new ArrayCollection();
        $this->roles = [self::DEFAULT_ROLE];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodeCommune(): ?string
    {
        return $this->codeCommune;
    }

    public function setCodeCommune(string $codeCommune): self
    {
        $this->codeCommune = $codeCommune;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Pret>
     */
    public function getPrets(): Collection
    {
        return $this->prets;
    }

    public function addPret(Pret $pret): self
    {
        if (!$this->prets->contains($pret)) {
            $this->prets[] = $pret;
            $pret->setAdherent($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): self
    {
        if ($this->prets->removeElement($pret)) {
            // set the owning side to null (unless already changed)
            if ($pret->getAdherent() === $this) {
                $pret->setAdherent(null);
            }
        }

        return $this;
    }           //affectes les roles de l'utilisateur
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantir que chaque utilisateur a au moins le rôle par défaut
        $roles[] = self::DEFAULT_ROLE;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->getMail();
    }

    public function eraseCredentials()
    {
        // Cette méthode peut rester vide
    }
}
