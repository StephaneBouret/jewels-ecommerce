<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Enum\PurchaseStatus;
use App\Event\PurchaseSuccessEvent;
use App\Security\Voter\PurchaseVoter;
use App\Stripe\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
final class PurchasePaymentSuccessController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly StripeService $stripeService
    ) {}

    #[IsGranted('ROLE_USER')]
    #[Route('/paiement/success/{id}', name: 'app_purchase_payment_success', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function success(
        Purchase $purchase,
        Request $request,
        EventDispatcherInterface $dispatcher
    ): Response {
        $this->denyAccessUnlessGranted(PurchaseVoter::VIEW, $purchase);

        if ($purchase->getStatus() === PurchaseStatus::PAID) {
            return $this->redirectToRoute('app_home');
        }

        $paymentIntentId = $request->query->get('payment_intent');

        if (!$paymentIntentId) {
            $this->addFlash('danger', 'Impossible de vérifier le paiement.');
            return $this->redirectToRoute('app_purchase_payment', [
                'id' => $purchase->getId(),
            ]);
        }

        $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

        if (
            $paymentIntent->status !== 'succeeded'
            || ($paymentIntent->metadata['purchase_id'] ?? null) !== (string) $purchase->getId()
        ) {
            $this->addFlash('danger', 'Le paiement n\'a pas pu être confirmé.');
            return $this->redirectToRoute('app_purchase_payment', [
                'id' => $purchase->getId(),
            ]);
        }

        $purchase->markPaid();

        $this->em->flush();

        $dispatcher->dispatch(new PurchaseSuccessEvent($purchase), PurchaseSuccessEvent::NAME);

        $this->addFlash('success', 'La commande a été payée et confirmée !');

        return $this->redirectToRoute('app_home');
    }
}
