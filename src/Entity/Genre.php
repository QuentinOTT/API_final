<?php

namespace App\Entity;

use App\Entity\Livre;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 * @ApiResource()
 * @UniqueEntity(
 *    fields={"libelle"},
 *    message="Ce genre existe déjà")
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="Le libelle doit faire au moins {{ limit }} caractères",
     *     maxMessage ="Le libelle doit faire au plus {{ limit }} caractères"
     * )
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="genre")
     * @ApiSubresource()
     */
    private $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getlivres(): Collection
    {
        return $this->livres;
    }

    public function addlivres(Livre $livres): self
    {
        if (!$this->livres->contains($livres)) {
            $this->livres[] = $livres;
            $livres->setGenre($this);
        }

        return $this;
    }

    public function removelivres(Livre $livres): self
    {
        if ($this->livres->removeElement($livres)) {
            // set the owning side to null (unless already changed)
            if ($livres->getGenre() === $this) {
                $livres->setGenre(null);
            }
        }

        return $this;
    }
}
