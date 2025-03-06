<?php

namespace App\Entity;

use App\Entity\Livre;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
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
     * @Groups({"listGenreSimple","listGenreFull"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Groups({"listGenreSimple","listGenreFull"})
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
     * @Groups({"listGenreFull"})
     */
    private $editeur;

    public function __construct()
    {
        $this->editeur = new ArrayCollection();
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
    public function getEditeur(): Collection
    {
        return $this->editeur;
    }

    public function addEditeur(Livre $editeur): self
    {
        if (!$this->editeur->contains($editeur)) {
            $this->editeur[] = $editeur;
            $editeur->setGenre($this);
        }

        return $this;
    }

    public function removeEditeur(Livre $editeur): self
    {
        if ($this->editeur->removeElement($editeur)) {
            // set the owning side to null (unless already changed)
            if ($editeur->getGenre() === $this) {
                $editeur->setGenre(null);
            }
        }

        return $this;
    }
}
