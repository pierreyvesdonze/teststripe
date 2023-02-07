<?php

namespace App\Controller\Admin;

use App\Repository\BannertopRepository;
use App\Repository\CategoryProductRepository;
use App\Repository\StarringProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomepageController extends AbstractController
{
    #[Route('/admin/homepage', name: 'admin_manage_homepage')]
    public function index(
        CategoryProductRepository $categoryProductRepository,
        StarringProductRepository $starringProductRepository,
        BannertopRepository $bannertopRepository

        ): Response
    {
        $categories      = $categoryProductRepository->findAllOrderedForHomepage();
        $starringProduct = $starringProductRepository->findAll();
        $bannerTop       = $bannertopRepository->findAll();

        if(!$bannerTop) {
            $bannerTop = null;
        } else {
            $bannerTop = $bannerTop[0];
        }
        
        return $this->render('admin/manage_homepage/index.html.twig', [
            'categories'      => $categories,
            'starringProduct' => $starringProduct,
            'bannerTop'       => $bannerTop
        ]);
    }
}
