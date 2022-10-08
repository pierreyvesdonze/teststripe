<?php

namespace App\Controller;

use App\Entity\CategoryProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Repository\UserRateRepository;
use App\Service\UserRateManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
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
        $products = $this->productRepository->findAllActivated();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/voir/{id}', name: 'product_show', methods: ['GET'])]
    public function show(
        Product $product,
        UserRateRepository $userRateRepository,
        UserRateManager $userRateManager
        ): Response
    {
        $userRates = $userRateRepository->findAllByProduct($product);
        
        // Calculate average of rates
        $totalAverageProduct = $userRateManager->calculAverage($userRates);
        
        return $this->render('product/show.html.twig', [
            'product'             => $product,
            'userRates'           => $userRates,
            'totalAverageProduct' => $totalAverageProduct
        ]);
    }

    #[Route('s/categorie/{id}', name: 'products_by_category', methods: ['GET'])]
    public function productsByCategory(CategoryProduct $category): Response
    {
        $products = $this->productRepository->findAllActivatedByCategory($category->getId());

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'category' => $category
        ]);
    }
}
