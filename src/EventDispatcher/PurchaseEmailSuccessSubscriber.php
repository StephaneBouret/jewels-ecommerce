<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use App\Service\SendMailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PurchaseEmailSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SendMailService $sendMail,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $defaultFrom,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            PurchaseSuccessEvent::NAME => 'onPurchaseSuccess',
        ];
    }

    public function onPurchaseSuccess(PurchaseSuccessEvent $event): void
    {
        $purchase = $event->getPurchase();
        $user = $purchase->getUser();

        if (!$user || !$user->getEmail()) {
            return;
        }

        $homeUrl = $this->urlGenerator->generate(
            'app_home',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->sendMail->sendMail(
            null,
            sprintf('Votre commande #%s est confirmée', $purchase->getNumber()),
            $user->getEmail(),
            sprintf('Votre commande #%s a bien été payée.', $purchase->getNumber()),
            'purchase_success_user',
            [
                'purchase' => $purchase,
                'user' => $user,
                'homeUrl' => $homeUrl,
            ]
        );
    }
}
