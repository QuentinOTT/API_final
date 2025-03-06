<?php

namespace App\Entity;

use App\Entity\Livre;
use App\Entity\Nationalite;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */
class Auteur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @GROUPS{{"listAuteurFull, listAuteurSimple"}}
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @GROUPS{{"listGenreFull, listAuteurFull, listAuteurSimple"}}
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @GROUPS{{"listGenreFull, listAuteurFull, listAuteurSimple"}}
     */
    private $prenom;

    /**
     * @ORM\ManyToOne(targetEntity=Nationalite::class, inversedBy="auteurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Relation;

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur")
     * @GROUPS{{"listAuteurFull"}}
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

    public function getRelation(): ?Nationalite
    {
        return $this->Relation;
    }

    public function setRelation(?Nationalite $Relation): self
    {
        $this->Relation = $Relation;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): self
    {
        if (!$this->livres->contains($livre)) {
            $this->livres[] = $livre;
            $livre->setAuteur($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): self
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getAuteur() === $this) {
                $livre->setAuteur(null);
            }
        }

        return $this;
    }
}
