<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminOrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository
    )
    {
        
    }
    #[Route('/admin/commandes', name: 'admin_orders')]
    public function index(): Response
    {
        $orders = $this->orderRepository->findAll();

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
