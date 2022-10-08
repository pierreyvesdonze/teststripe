<?php

namespace App\Controller;

use App\Repository\CategoryProductRepository;
use App\Repository\ProductRepository;
use App\Repository\StarringProductRepository;
use App\Repository\UserRateRepository;
use App\Service\StockManager;
use App\Service\UserRateManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(
        CategoryProductRepository $categoryProductRepository,
        StarringProductRepository $starringProductRepository,
        UserRateRepository $userRateRepository,
        UserRateManager $userRateManager,
        StockManager $stockManager
        ): Response
    {
        $categories      = $categoryProductRepository->findAllOrderedForHomepage();
        $starringProduct = $starringProductRepository->findAll();
        $userRates       = $userRateRepository->findAllByProduct($starringProduct[0]->getProduct());

        // Calculate average of rates
        $totalAverageProduct = $userRateManager->calculAverage($userRates);

        // Total products in stock
        $totalStock = $stockManager->totalProductsInStock();

        return $this->render('main/index.html.twig', [
            'categories'          => $categories,
            'starringProduct'     => $starringProduct[0],
            'userRates'           => $userRates,
            'totalAverageProduct' => $totalAverageProduct,
            'totalStock'          => $totalStock
        ]);
    }

    public function navbar(
        CategoryProductRepository $categoriesRepository
        )
    {
        return $this->render('nav/nav.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route('/rechercher', name: 'search_product')]
    public function searchProduct(
        Request $request,
        ProductRepository $productRepository
        ) 
    {
        $response = null;

        if ($request->isMethod('POST')) {
            $query    = $request->request->get('search');
            $response = $productRepository->findProductsByKeyword($query);
        }
        return $this->render('search/search.result.html.twig', [
            'products' => $response
        ]);
    }
}
