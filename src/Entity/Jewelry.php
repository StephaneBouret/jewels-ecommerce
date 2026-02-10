<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\JewelryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé.')]
#[ORM\Entity(repositoryClass: JewelryRepository::class)]
class Jewelry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 5, minMessage: "Le nom doit avoir au moins {{ limit }} caractères")]
    private ?string $name = null;

    #[ORM\Column(length: 191, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'jewelries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, JewelryVariant>
     */
    #[ORM\OneToMany(targetEntity: JewelryVariant::class, mappedBy: 'jewelry', cascade: ['persist'], orphanRemoval: true)]
    private Collection $variants;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, JewelryVariant>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(JewelryVariant $variant): static
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setJewelry($this);
        }

        return $this;
    }

    public function removeVariant(JewelryVariant $variant): static
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getJewelry() === $this) {
                $variant->setJewelry(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getVariantsSummary(): string
    {
        $lines = [];

        foreach ($this->getVariants() as $variant) {
            $labelColor = match ($variant->getColor()) {
                'silver' => 'Argent',
                'gold'   => 'Or',
                default  => $variant->getColor(),
            };

            $price = number_format($variant->getPriceCents() / 100, 2, ',', ' ') . ' €';
            $qty   = $variant->getQuantity();

            $img = $variant->getImageName()
                ? sprintf(
                    '<img src="/images/jewelry/%s" style="height:38px;border-radius:6px;margin-right:8px;object-fit:cover;">',
                    htmlspecialchars($variant->getImageName(), ENT_QUOTES)
                )
                : '';

            $desc = $variant->getDescription()
                ? ' — <em style="opacity:.85">' . nl2br(htmlspecialchars((string) $variant->getDescription(), ENT_QUOTES)) . '</em>'
                : '';

            $lines[] = sprintf(
                '%s<strong>%s</strong> — %s — stock: %d%s',
                $img,
                $labelColor,
                $price,
                $qty,
                $desc
            );
        }

        return $lines ? implode('<br>', $lines) : '<em>Aucun détail</em>';
    }
}
