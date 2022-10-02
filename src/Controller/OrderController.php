<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {}
    #[Route('/commandes', name: 'orders')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('order/index.html.twig');
    }

    #[Route('/commande/nouvelle', name: 'order_new')]
    public function new(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        
        $order = new Order;
        $order->setUser($this->getUser());
        $order->setReference('ref' . uniqid());
        $order->setCreatedAt(new \DateTime('now'));

        $cart  = $this->getUser()->getCart();
        $price = 0;

        foreach ($cart->getCartLines() as $cartLine) {
            $orderLine = new OrderLine;
            $orderLine->setProduct($cartLine->getProduct());
            $orderLine->setOrderId($order);
            $orderLine->setQuantity($cartLine->getQuantity());
            $this->em->persist($orderLine);
            $price += $cartLine->getProduct()->getPrice();
        }

        $order->setPrice($price);

        $this->em->persist($order);
        $this->em->flush();

        return $this->redirectToRoute('payment_confirmation');
    }
}
