<?php

namespace App\Repository;

use App\Entity\JewelryVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JewelryVariant>
 */
class JewelryVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JewelryVariant::class);
    }

    /**
     * @return JewelryVariant[]
     */
    public function findLatestForHomepage(int $limit = 4): array
    {
        return $this->createQueryBuilder('v')
            ->addSelect('j', 'c')
            ->join('v.jewelry', 'j')
            ->join('j.category', 'c')
            ->orderBy('v.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findForCatalog(
        ?string $categorySlug = null,
        ?string $sort = null,
        ?string $search = null
    ): array {
        $qb = $this->createQueryBuilder('v')
            ->addSelect('j', 'c')
            ->join('v.jewelry', 'j')
            ->join('j.category', 'c');

        if ($categorySlug && $categorySlug !== 'all') {
            $qb->andWhere('c.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug);
        }

        if ($search) {
            $qb->andWhere('LOWER(j.name) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower(trim($search)) . '%');
        }

        switch ($sort) {
            case 'price_asc':
                $qb->orderBy('v.priceCents', 'ASC');
                break;

            case 'price_desc':
                $qb->orderBy('v.priceCents', 'DESC');
                break;

            case 'latest':
            default:
                $qb->orderBy('v.id', 'DESC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return JewelryVariant[] Returns an array of JewelryVariant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?JewelryVariant
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
