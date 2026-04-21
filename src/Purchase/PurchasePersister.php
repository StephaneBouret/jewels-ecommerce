<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use App\Enum\PurchaseStatus;
use App\Service\PurchaseNumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PurchasePersister
{
    public function __construct(
        private Security $security,
        private CartService $cartService,
        private EntityManagerInterface $em,
        private PurchaseNumberGenerator $purchaseNumber
    ) {}

    public function storePurchase(Purchase $purchase): void
    {
        /** User|null $user */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('Aucun utilisateur connecté pour enregistrer la commande.');
        }

        $purchase
            ->setUser($user)
            ->setTotal($this->cartService->getTotal())
            ->setStatus(PurchaseStatus::PENDING)
            ->setNumber($this->purchaseNumber->generate());

        $this->em->persist($purchase);

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $product = $cartItem->getProduct();

            $purchaseItem = (new PurchaseItem())
                ->setPurchase($purchase)
                ->setProduct($product)
                ->setProductName((string) $product->getJewelry()?->getName())
                ->setProductColor($product->getColorEnum()->label())
                ->setQuantity($cartItem->getQty())
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($product->getPriceCents());

            $this->em->persist($purchaseItem);
        }
    }
}
