<?php

namespace App\Entity;

use App\Enum\JewelryColor;
use App\Repository\JewelryVariantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: JewelryVariantRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_jewelry_color', columns: ['jewelry_id', 'color'])]
#[Vich\Uploadable]
class JewelryVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'variants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Jewelry $jewelry = null;

    #[Assert\NotBlank(message: 'La couleur est obligatoire.')]
    #[Assert\Choice(
        callback: [JewelryColor::class, 'values'],
        message: 'Couleur invalide'
    )]
    #[ORM\Column(length: 20)]
    private string $color = JewelryColor::SILVER->value;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif ou nul.')]
    private int $priceCents = 0;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[Assert\PositiveOrZero(message: 'La quantité doit être positive ou nulle.')]
    private int $quantity = 0;

    #[Assert\Image(
        maxSize: '2M',
        maxSizeMessage: 'L\'image est trop lourde ({{ size }} {{ suffix }}). Le maximum autorisé est {{ limit }} {{ suffix }}',
        minWidth: 100,
        minWidthMessage: 'La largeur de l\'image est trop petite ({{ width }}px). Le minimum est {{ min_width }}px.',
        minHeight: 100,
        minHeightMessage: 'La hauteur est trop faible ({{ height }}px). Le minimum est {{ min_height }}px.',
        mimeTypes: [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp'
        ],
        mimeTypesMessage: 'Le type MIME du fichier n\'est pas valide ({{ type }}). Les formats autorisés sont {{ types }}'
    )]
    #[Vich\UploadableField(mapping: 'jewelry_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJewelry(): ?Jewelry
    {
        return $this->jewelry;
    }

    public function setJewelry(?Jewelry $jewelry): static
    {
        $this->jewelry = $jewelry;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(JewelryColor|string $color): self
    {
        $this->color = $color instanceof JewelryColor ? $color->value : $color;

        return $this;
    }

    public function getColorEnum(): JewelryColor
    {
        return JewelryColor::from($this->color);
    }

    public function getPriceCents(): int
    {
        return $this->priceCents;
    }

    public function setPriceCents(int $priceCents): self
    {
        if ($priceCents < 0) {
            throw new \InvalidArgumentException('Le prix ne peut pas être négatif.');
        }

        $this->priceCents = $priceCents;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('La quantité ne peut pas être négative.');
        }

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%s)',
            $this->jewelry?->getName() ?? 'Bijou',
            $this->getColorEnum()->label()
        );
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
