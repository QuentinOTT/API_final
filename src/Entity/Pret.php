<?php

namespace App\Entity;

use App\Repository\PretRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PretRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/prets"
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/prets",
 *             "security"="is_granted('ROLE_ADHERENT')",
 *             "security_message"="Seuls les adhérents peuvent créer des prêts",
 *             "denormalization_context"={
 *                 "groups"={"post_adherent"}
 *             }
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/prets/{id}",
 *             "controller"="App\Controller\PretController::getPret"
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/prets/{id}",
 *             "security"="is_granted('ROLE_MANAGER')",
 *             "security_message"="Seuls les managers peuvent modifier un prêt",
 *             "denormalization_context"={
 *                 "groups"={"put_manager"}
 *             }
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/prets/{id}",
 *             "security"="is_granted('ROLE_ADMIN')",
 *             "security_message"="Seuls les administrateurs peuvent supprimer un prêt"
 *         }
 *     }
 * )
 */
class Pret
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin"})
     */
    private $datePret;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_role_manager", "get_role_admin"})
     */
    private $dateRetourPrevue;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_role_manager", "get_role_admin"})
     */
    private $dateRetourReelle;

    /**
     * @ORM\ManyToOne(targetEntity=Livre::class, inversedBy="prets")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin"})
     */
    private $livre;

    /**
     * @ORM\ManyToOne(targetEntity=Adherent::class, inversedBy="prets")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_role_adherent", "get_role_manager", "get_role_admin"})
     */
    private $adherent;

    public function __construct()
    {
        $this->datePret = new \DateTime();
        $dateRetourPrevue=date('Y-m-d H:i:s',strtotime('15 days', $this->getDatePret()->getTimestamp()));
        $dateRetourPrevue =\DateTime::createFromFormat('Y-m-d H:i:s', $dateRetourPrevue);
        $this->dateRetourPrevue = $dateRetourPrevue;
        $this->dateRetourReelle = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePret(): ?\DateTimeInterface
    {
        return $this->datePret;
    }

    public function setDatePret(\DateTimeInterface $datePret): self
    {
        $this->datePret = $datePret;

        return $this;
    }

    public function getDateRetourPrevue(): ?\DateTimeInterface
    {
        return $this->dateRetourPrevue;
    }

    public function setDateRetourPrevue(?\DateTimeInterface $dateRetourPrevue): self
    {
        $this->dateRetourPrevue = $dateRetourPrevue;

        return $this;
    }

    public function getDateRetourReelle(): ?\DateTimeInterface
    {
        return $this->dateRetourReelle;
    }

    public function setDateRetourReelle(?\DateTimeInterface $dateRetourReelle): self
    {
        $this->dateRetourReelle = $dateRetourReelle;

        return $this;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;

        return $this;
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }
}
