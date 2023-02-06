<?php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Service\DiscountManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $privateKey;

    public function __construct()
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    #[Route('paiement', name: 'payment')]
    public function payment(DiscountManager $discountManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $cart  = $this->getUser()->getCart();
        $total = 0;

        foreach ($cart->getCartLines() as $cartLine) {
            $total += ($cartLine->getProduct()->getPrice() * $cartLine->getQuantity());
        }

        if ($cart->getDiscount() !== null) {
            $discount       = $discountManager->getDiscount($cart, $total);
            $total = $total - $discount;
        }

        // Stripe API
        \Stripe\Stripe::setApiKey($this->privateKey);
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $total * 100,
            'currency' => 'eur'
        ]);
        $intentSecret = $intent['client_secret'];
        
        return $this->render('payment/index.html.twig', [
            'intentSecret' => $intentSecret,
            'total'        => $total
        ]);
    }

    #[Route('paiement/confirmation', name: 'payment_confirmation')]
    public function paymentConfirmation(): Response {

        return $this->render('payment/confirmation.html.twig');
    }
}
