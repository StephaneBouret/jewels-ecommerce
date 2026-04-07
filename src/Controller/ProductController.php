<?php

namespace App\Controller;

use App\Entity\JewelryVariant;
use App\Repository\CategoryRepository;
use App\Repository\JewelryVariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits', name: 'app_product_')]
final class ProductController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        JewelryVariantRepository $jewelryVariantRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $products = $jewelryVariantRepository->findForCatalog(
            categorySlug: 'all',
            sort: 'latest',
            search: null
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findUsedOnHomepage(),
            'currentCategory' => 'all',
            'currentSort' => 'latest',
            'currentSearch' => '',
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(
        Request $request,
        JewelryVariantRepository $jewelryVariantRepository
    ): Response {
        $category = (string) $request->query->get('category', 'all');
        $sort = (string) $request->query->get('sort', 'latest');
        $search = trim((string) $request->query->get('search', ''));

        $products = $jewelryVariantRepository->findForCatalog(
            categorySlug: $category,
            sort: $sort,
            search: $search !== '' ? $search : null
        );

        return $this->render('product/_products_grid.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(JewelryVariant $product): Response
    {
        $jewelry = $product->getJewelry();
        $variants = $jewelry?->getVariants() ?? [];

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'jewelry' => $jewelry,
            'variants' => $variants,
        ]);
    }
}
