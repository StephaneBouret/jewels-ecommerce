<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Enum\PurchaseStatus;
use App\Security\Voter\PurchaseVoter;
use App\Stripe\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
final class PurchasePaymentController extends AbstractController
{
    public function __construct(private readonly StripeService $stripeService) {}

    #[IsGranted('ROLE_USER')]
    #[Route('/paiement/{id}', name: 'app_purchase_payment', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showPaymentForm(Purchase $purchase): Response
    {
        $this->denyAccessUnlessGranted(PurchaseVoter::PAY, $purchase);

        if ($purchase->getStatus() !== PurchaseStatus::PENDING) {
            return $this->redirectToRoute('app_home');
        }

        $paymentIntent = $this->stripeService->getPaymentIntentForPurchase($purchase);

        return $this->render('purchase/payment.html.twig', [
            'purchase' => $purchase,
            'clientSecret' => $paymentIntent->client_secret,
            'stripePublicKey' => $this->stripeService->getPublicKey(),
        ]);
    }
}

