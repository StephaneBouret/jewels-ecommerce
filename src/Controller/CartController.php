<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Entity\JewelryVariant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier', name: 'app_cart_')]
final class CartController extends AbstractController
{
    #[Route('', name: 'show', methods: ['GET'])]
    public function show(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getDetailedCartItems(),
            'total' => $cartService->getTotal(),
            'count' => $cartService->count(),
        ]);
    }

    #[Route('/add/{id}', name: 'add', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function add(
        JewelryVariant $product,
        Request $request,
        CartService $cartService
    ): JsonResponse {
        $qty = max(1, (int) $request->request->get('qty', 1));
        $cartService->add($product->getId(), $qty);

        return $this->jsonCart($cartService, 'Produit ajouté au panier.');
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function update(
        JewelryVariant $product,
        Request $request,
        CartService $cartService
    ): JsonResponse {
        $qty = max(0, (int) $request->request->get('qty', 1));
        $cartService->setQuantity($product->getId(), $qty);

        return $this->jsonCart($cartService, 'Panier mis à jour.');
    }

    #[Route('/remove/{id}', name: 'remove', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function remove(
        JewelryVariant $product,
        CartService $cartService
    ): JsonResponse {
        $cartService->remove($product->getId());

        return $this->jsonCart($cartService, 'Produit supprimé du panier.');
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clear(CartService $cartService): JsonResponse
    {
        $cartService->clear();

        return $this->jsonCart($cartService, 'Panier vidé.');
    }

    private function jsonCart(CartService $cartService, string $message): JsonResponse
    {
        $html = $this->renderView('cart/_content.html.twig', [
            'items' => $cartService->getDetailedCartItems(),
            'total' => $cartService->getTotal(),
            'count' => $cartService->count(),
        ]);

        return $this->json([
            'success' => true,
            'message' => $message,
            'count' => $cartService->count(),
            'total' => $cartService->getTotal(),
            'html' => $html,
        ]);
    }
}
