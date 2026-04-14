<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\JewelryVariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        JewelryVariantRepository $jewelryVariantRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $heroProducts = $jewelryVariantRepository->findLatestForHomepage(4);
        $filteredProducts = $jewelryVariantRepository->findLatestForHomepage(8);

        return $this->render('home/index.html.twig', [
            'heroProducts' => $heroProducts,
            'filteredProducts' => $filteredProducts,
            'categories' => $categoryRepository->findUsedOnHomepage(),
        ]);
    }
}
