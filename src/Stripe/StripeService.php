<?php

namespace App\Stripe;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $client;

    public function __construct(
        private readonly string $secretKey,
        private readonly string $publicKey
    ) {
        $this->client = new StripeClient($this->secretKey);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function createPaymentIntent(int $amount, array $metadata = [], ?string $description = null): PaymentIntent
    {
        return $this->client->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'eur',
            'payment_method_types' => ['card'], // 'paypal' à ajouter dans le tableau ['card', 'paypal'] si activé dans Stripe
            'metadata' => array_filter($metadata, fn($v) => $v !== null && $v !== ''),
            'description' => $description,
        ]);
    }

    public function getPaymentIntentForPurchase(Purchase $purchase): PaymentIntent
    {
        $user = $purchase->getUser();

        return $this->createPaymentIntent(
            (int) $purchase->getTotal(),
            [
                'kind' => 'purchase',
                'purchase_id' => (string) $purchase->getId(),
                'user_id' => $user ? (string) $user->getId() : null,
                'user_email' => $user ? (string) $user->getEmail() : null,
            ],
            sprintf('Commande bijoux #%s', $purchase->getNumber())
        );
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return $this->client->paymentIntents->retrieve($paymentIntentId);
    }
}
