<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\PurchaseCheckoutFormType;
use App\Purchase\PurchasePersister;
use App\Security\Voter\PurchaseVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
final class PurchaseCheckoutController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/checkout', name: 'app_purchase_checkout', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        CartService $cartService,
        PurchasePersister $persister,
        EntityManagerInterface $em
    ): Response {
        $cartItems = $cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas valider une commande avec un panier vide.');
            return $this->redirectToRoute('app_cart_show');
        }

        $purchase = new Purchase();

        $form = $this->createForm(PurchaseCheckoutFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $persister->storePurchase($purchase);
            $em->flush();

            $cartService->clear();

            $this->addFlash('success', 'Votre commande a bien été enregistrée.');

            return $this->redirectToRoute('app_purchase_payment', [
                'id' => $purchase->getId(),
            ]);
        }

        return $this->render('purchase/checkout.html.twig', [
            'user' => $this->getUser(),
            'items' => $cartItems,
            'total' => $cartService->getTotal(),
            'confirmationForm' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/confirmation/{id}', name: 'app_purchase_confirm', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function confirm(Purchase $purchase): Response
    {
        $this->denyAccessUnlessGranted(PurchaseVoter::VIEW, $purchase);

        return $this->render('purchase/confirmation.html.twig', [
            'purchase' => $purchase,
        ]);
    }
}
