<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $subtotal_amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tax_amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $total_amount = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $estimated_delivery_time = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orderRelation', cascade: ['persist', 'remove'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSubtotalAmount(): ?string
    {
        return $this->subtotal_amount;
    }

    public function setSubtotalAmount(?string $subtotal_amount): static
    {
        $this->subtotal_amount = $subtotal_amount;

        return $this;
    }

    public function getTaxAmount(): ?string
    {
        return $this->tax_amount;
    }

    public function setTaxAmount(?string $tax_amount): static
    {
        $this->tax_amount = $tax_amount;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->total_amount;
    }

    public function setTotalAmount(?string $total_amount): static
    {
        $this->total_amount = $total_amount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getEstimatedDeliveryTime(): ?\DateTime
    {
        return $this->estimated_delivery_time;
    }

    public function setEstimatedDeliveryTime(?\DateTime $estimated_delivery_time): static
    {
        $this->estimated_delivery_time = $estimated_delivery_time;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrderRelation($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrderRelation() === $this) {
                $orderItem->setOrderRelation(null);
            }
        }

        return $this;
    }
}
