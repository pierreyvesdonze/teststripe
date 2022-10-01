<?php

namespace App\Controller;

use App\Entity\CategoryProduct;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
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
