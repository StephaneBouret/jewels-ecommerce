<?php

namespace App\Security\Voter;

use App\Entity\Purchase;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PurchaseVoter extends Voter
{
    public const VIEW = 'PURCHASE_VIEW';
    public const PAY = 'PURCHASE_PAY';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::PAY], true)
            && $subject instanceof Purchase;
    }

    /**
     * @param Purchase $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::PAY => $this->canPay($subject, $user),
            default => false,
        };
    }

    private function canView(Purchase $purchase, User $user): bool
    {
        return $purchase->getUser() === $user;
    }

    private function canPay(Purchase $purchase, User $user): bool
    {
        return $purchase->getUser() === $user
            && $purchase->isPending();
    }
}
