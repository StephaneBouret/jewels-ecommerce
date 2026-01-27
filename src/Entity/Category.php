<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    #[Assert\NotBlank(message: "La catégorie est obligatoire")]
    #[Assert\Length(min: 5, minMessage: "La catégorie doit avoir au moins {{ limit }} caractères")]
    private ?string $name = null;

    #[ORM\Column(length: 191, unique: true)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Jewelry>
     */
    #[ORM\OneToMany(targetEntity: Jewelry::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $jewelries;

    public function __construct()
    {
        $this->jewelries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Jewelry>
     */
    public function getJewelries(): Collection
    {
        return $this->jewelries;
    }

    public function addJewelry(Jewelry $jewelry): static
    {
        if (!$this->jewelries->contains($jewelry)) {
            $this->jewelries->add($jewelry);
            $jewelry->setCategory($this);
        }

        return $this;
    }

    public function removeJewelry(Jewelry $jewelry): static
    {
        if ($this->jewelries->removeElement($jewelry)) {
            // set the owning side to null (unless already changed)
            if ($jewelry->getCategory() === $this) {
                $jewelry->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
