<?php

namespace App\Controller\Admin;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/accueil')]
class AdminMainController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderLineRepository $orderLineRepository,
        private ProductRepository $productRepository,
        private UserRepository $userRepository
    ) {
    }
    #[Route('/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        // Total of sell products
        $totalOrders = $this->orderLineRepository->createQueryBuilder('o')
            ->select('SUM(o.quantity)')
            ->getQuery()
            ->getSingleScalarResult();

        // Total turnover of the month
        $totalSellArray     = $this->orderRepository->findCaByDate();
        $totalSellThisMonth = 0;
        foreach ($totalSellArray as $key => $order) {
            $totalSellThisMonth += $order->getPrice();
        }

        // Total products for sale
        $totalProducts = count($this->productRepository->findAll());

        //Total stock
        $totalStock = $this->productRepository->createQueryBuilder('p')
            ->select('SUM(p.stock)')
            ->getQuery()
            ->getSingleScalarResult();

        // Total Users
        $totalUsers = count($this->userRepository->findAll());

        return $this->render('admin/main/index.html.twig', [
            'totalOrders'        => $totalOrders,
            'totalSellThisMonth' => $totalSellThisMonth,
            'totalProducts'      => $totalProducts,
            'totalStock'         => $totalStock,
            'totalUsers'         => $totalUsers
        ]);
    }
}
