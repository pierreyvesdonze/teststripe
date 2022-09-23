<?php

namespace App\Controller;

use App\Repository\CategoryProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    public function navbar(CategoryProductRepository $categoriesRepository)
    {
        return $this->render('nav/nav.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }
}
