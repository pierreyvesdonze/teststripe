<?php

namespace App\Controller;

use App\Repository\CategoryProductRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoryProductRepository $categoryProductRepository): Response
    {
        $categories = $categoryProductRepository->findAllOrderedForHomepage();

        return $this->render('main/index.html.twig', [
            'categories' => $categories
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
