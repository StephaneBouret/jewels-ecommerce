<?php

namespace App\Event;

use App\Entity\Purchase;

class PurchaseSuccessEvent
{
    public const NAME = 'purchase.success';

    public function __construct(private readonly Purchase $purchase) {}

    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }
}
