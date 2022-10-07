<?php

namespace App\Controller\Admin;

use App\Repository\CategoryProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomepageController extends AbstractController
{
    #[Route('/admin/homepage', name: 'admin_manage_homepage')]
    public function index(CategoryProductRepository $categoryProductRepository): Response
    {
        $categories = $categoryProductRepository->findAllOrderedForHomepage();
        
        return $this->render('admin/manage_homepage/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
