<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\OrderRepository;
use App\Service\StockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private OrderRepository $orderRepository
        )
    {}
    
    #[Route('/commandes', name: 'orders')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $orders = $this->orderRepository->findByUserDesc($this->getUser());

        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/commande/nouvelle', name: 'order_new')]
    public function new(
        StockManager $stockManager,
        Request $request
        ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        // Get Address delivery
        $session = $request->getSession();
        $addressType = $session->get('address');     
        
        /**
         * @var Order $order
         */
        $order = new Order;
        $order->setUser($this->getUser());
        $order->setReference('ref' . uniqid());
        $order->setCreatedAt(new \DateTime('now'));
        $order->setAddress($addressType);

        /**
         * @var Cart $cart
         */
        $cart  = $this->getUser()->getCart();
        $price = 0;

        // Add lines to order
        foreach ($cart->getCartLines() as $cartLine) {
            $orderLine = new OrderLine;
            $orderLine->setProduct($cartLine->getProduct());
            $orderLine->setOrderId($order);
            $orderLine->setQuantity($cartLine->getQuantity());
            
            $this->em->persist($orderLine);

            // Update stock
            $stockManager->removeOneFromStock($cartLine->getProduct());

            // total price
            $price += $cartLine->getProduct()->getPrice();
        }

        $order->setPrice($price);

        $this->em->persist($order);
        $this->em->flush();

        return $this->redirectToRoute('payment_confirmation');
    }
}
