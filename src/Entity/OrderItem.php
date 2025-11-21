<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRelation = null;

    #[ORM\Column]
    private ?int $pizza_id = null;

    #[ORM\Column(length: 255)]
    private ?string $pizza_name = null;

    #[ORM\Column(length: 50)]
    private ?string $size = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $unit_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $item_subtotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $tax_rate = '0.21';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $tax_amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $item_total = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderRelation(): ?Order
    {
        return $this->orderRelation;
    }

    public function setOrderRelation(?Order $orderRelation): static
    {
        $this->orderRelation = $orderRelation;

        return $this;
    }

    public function getPizzaId(): ?int
    {
        return $this->pizza_id;
    }

    public function setPizzaId(int $pizza_id): static
    {
        $this->pizza_id = $pizza_id;

        return $this;
    }

    public function getPizzaName(): ?string
    {
        return $this->pizza_name;
    }

    public function setPizzaName(string $pizza_name): static
    {
        $this->pizza_name = $pizza_name;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

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

    public function getUnitPrice(): ?string
    {
        return $this->unit_price;
    }

    public function setUnitPrice(string $unit_price): static
    {
        $this->unit_price = $unit_price;

        return $this;
    }

    public function getItemSubtotal(): ?string
    {
        return $this->item_subtotal;
    }

    public function setItemSubtotal(string $item_subtotal): static
    {
        $this->item_subtotal = $item_subtotal;

        return $this;
    }

    public function getTaxRate(): ?string
    {
        return $this->tax_rate;
    }

    public function setTaxRate(string $tax_rate): static
    {
        $this->tax_rate = $tax_rate;

        return $this;
    }

    public function getTaxAmount(): ?string
    {
        return $this->tax_amount;
    }

    public function setTaxAmount(string $tax_amount): static
    {
        $this->tax_amount = $tax_amount;

        return $this;
    }

    public function getItemTotal(): ?string
    {
        return $this->item_total;
    }

    public function setItemTotal(string $item_total): static
    {
        $this->item_total = $item_total;

        return $this;
    }
}
