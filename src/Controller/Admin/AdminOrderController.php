<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminOrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em
    )
    {}

    #[Route('/admin/commandes', name: 'admin_orders')]
    public function index(): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $orders = $this->orderRepository->findAllByDesc();

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/admin/commande/modifier/status/{id}', name: 'admin_switch_order_status')]
    public function updateStatus(Order $order): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $order->getStatus() == false ? $order->setStatus(true) : $order->setStatus(false);
        $this->em->flush();

        $this->addFlash('success', 'Statut de la commande modifié !');

        return $this->redirectToRoute('admin_orders');
    }
}
