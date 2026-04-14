<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Entity\JewelryVariant;
use App\Repository\JewelryVariantRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(private RequestStack $requestStack, private JewelryVariantRepository $jewelryVariantRepository) {}

    private function getSessionCart(): array
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    private function saveSessionCart(array $cart): void
    {
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function clear(): void
    {
        $this->saveSessionCart([]);
    }

    public function add(int $id, int $qty = 1): void
    {
        $cart = $this->getSessionCart();

        if (!isset($cart[$id])) {
            $cart[$id] = 0;
        }

        $cart[$id] += max(1, $qty);

        $this->saveSessionCart($cart);
    }

    public function setQuantity(int $id, int $qty): void
    {
        $cart = $this->getSessionCart();

        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }

        $this->saveSessionCart($cart);
    }

    public function decrement(int $id): void
    {
        $cart = $this->getSessionCart();

        if (!isset($cart[$id])) {
            return;
        }

        if ($cart[$id] <= 1) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }

        $this->saveSessionCart($cart);
    }

    public function remove(int $id): void
    {
        $cart = $this->getSessionCart();
        unset($cart[$id]);
        $this->saveSessionCart($cart);
    }

    public function count(): int
    {
        return array_sum($this->getSessionCart());
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getDetailedCartItems() as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getSessionCart() as $id => $qty) {
            /** @var JewelryVariant|null $product */
            $product = $this->jewelryVariantRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }
}
