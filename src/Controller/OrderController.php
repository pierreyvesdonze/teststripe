<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {}

    #[Route('/commande/nouvelle', name: 'order_new')]
    public function index(): Response
    {
        $order = new Order;
        $order->setUser($this->getUser());
        $order->setReference('ref' . uniqid());
        $order->setCreatedAt(new \DateTime('now'));

        $cart  = $this->getUser()->getCart();
        $price = 0;

        foreach ($cart->getCartLines() as $cartLine) {
            $order->addProduct($cartLine->getProduct());
            $price += $cartLine->getProduct()->getPrice();
        }

        $order->setPrice($price);

        $this->em->persist($order);
        $this->em->flush();

        return $this->redirectToRoute('payment_confirmation');
    }
}
