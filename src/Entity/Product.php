<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, CartEntry>
     */
    #[ORM\OneToMany(targetEntity: CartEntry::class, mappedBy: 'product')]
    private Collection $cartEntries;

    public function __construct()
    {
        $this->cartEntries = new ArrayCollection();
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, CartEntry>
     */
    public function getCartEntries(): Collection
    {
        return $this->cartEntries;
    }

    public function addCartEntry(CartEntry $cartEntry): static
    {
        if (!$this->cartEntries->contains($cartEntry)) {
            $this->cartEntries->add($cartEntry);
            $cartEntry->setProduct($this);
        }

        return $this;
    }

    public function removeCartEntry(CartEntry $cartEntry): static
    {
        if ($this->cartEntries->removeElement($cartEntry)) {
            // set the owning side to null (unless already changed)
            if ($cartEntry->getProduct() === $this) {
                $cartEntry->setProduct(null);
            }
        }

        return $this;
    }
}
