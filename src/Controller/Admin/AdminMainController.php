<?php

namespace App\Controller\Admin;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/accueil')]
class AdminMainController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderLineRepository $orderLineRepository
    )
    {
    }
    #[Route('/index', name: 'admin_main', methods: ['GET'])]
    public function index(): Response
    {
        $totalOrders = $this->orderLineRepository->createQueryBuilder('o')
            ->select('count(o.product)')
            ->getQuery()
            ->getSingleScalarResult();

        $totalSellArray     = $this->orderRepository->findCaByDate();
        $totalSellThisMonth = 0;
        
        foreach ($totalSellArray as $key => $order) {
            $totalSellThisMonth += $order->getPrice();
        }

        return $this->render('admin/main/index.html.twig', [
            'totalOrders'       => $totalOrders,
            'totalSellThisMonth' => $totalSellThisMonth
        ]);
    }
}