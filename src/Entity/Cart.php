<?php

namespace App\Entity;

use App\Repository\CartRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, CartEntry>
     */
    #[ORM\OneToMany(targetEntity: CartEntry::class, mappedBy: 'cart', cascade: ['persist', 'remove'])]
    private Collection $entries;

    #[ORM\Column(nullable: true)]
    private ?bool $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transaction = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, CartEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(CartEntry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setCart($this);
        }

        return $this;
    }

    public function removeEntry(CartEntry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getCart() === $this) {
                $entry->setCart(null);
            }
        }

        return $this;
    }

    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this->entries as $entry) {
            $total += $entry->getTotal();
        }

        return $total;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTransaction(): ?string
    {
        return $this->transaction;
    }

    public function setTransaction(?string $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }
}
