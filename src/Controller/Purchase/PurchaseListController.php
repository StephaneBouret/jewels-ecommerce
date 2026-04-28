<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use App\Enum\PurchaseStatus;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commandes')]
final class PurchaseListController extends AbstractController
{
    #[Route('', name: 'app_purchase_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page.')]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $purchases = $purchaseRepository->createQueryBuilder('p')
            ->addSelect('pi')
            ->leftJoin('p.purchaseItems', 'pi')
            ->andWhere('p.user = :user')
            ->andWhere('p.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', [
                PurchaseStatus::PAID,
                PurchaseStatus::PENDING,
                PurchaseStatus::CANCELLED,
            ])
            ->orderBy('p.purchasedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('purchase/list.html.twig', [
            'purchases' => $purchases,
        ]);
    }
}
