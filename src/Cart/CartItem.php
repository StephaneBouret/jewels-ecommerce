<?php

namespace App\Cart;

use App\Entity\JewelryVariant;

class CartItem
{
    public function __construct(public JewelryVariant $product, public int $qty) {}

    public function getProduct(): JewelryVariant
    {
        return $this->product;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
    
    public function getTotal(): int
    {
        return $this->product->getPriceCents() * $this->qty;
    }
}
