<?php

namespace App\Controller;

use App\Entity\CategoryProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Repository\UserRateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository
        )
    {
    }

    #[Route('s', name: 'products', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $this->productRepository->findAllActivated(),
        ]);
    }

    #[Route('/show/{id}', name: 'product_show', methods: ['GET'])]
    public function show(
        Product $product,
        UserRateRepository $userRateRepository
        ): Response
    {
        $userRates = $userRateRepository->findAllByProduct($product);
        
        // Calculate average of rates
        $productAverageRate  = 0;
        $totalAverageProduct = 0;
        foreach ($userRates as $userRate) {
            $productAverageRate += $userRate->getRate();
        }

        if ($productAverageRate > 0) {
            $totalAverageProduct = $productAverageRate / count($userRates);
        } else {
            $totalAverageProduct = 0;
        }
        
        return $this->render('product/show.html.twig', [
            'product'             => $product,
            'userRates'           => $userRates,
            'totalAverageProduct' => $totalAverageProduct
        ]);
    }

    #[Route('s/category/{id}', name: 'products_by_category', methods: ['GET'])]
    public function productsByCategory(CategoryProduct $category): Response
    {
        $products = $this->productRepository->findAllActivatedByCategory($category->getId());

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'category' => $category
        ]);
    }
}
