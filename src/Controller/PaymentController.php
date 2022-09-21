<?php

namespace App\Controller;

use App\Entity\Product;
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

    #[Route('payment/{id}', name: 'payment')]
    public function payment(
        Product $product
    ): Response
    {
        \Stripe\Stripe::setApiKey($this->privateKey);

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $product->getPrice() * 100,
            'currency' => 'eur'
        ]);

        $intentSecret = $intent['client_secret'];

        if (!$this->getUser()) { 
            return $this->redirectToRoute('login');
        }
        return $this->render('payment/index.html.twig', [
            'intentSecret' => $intentSecret
        ]);
    }

    #[Route('pay/thanks/confirmation', name: 'payment_confirmation')]
    public function paymentConfirmation(): Response {

        return $this->render('payment/confirmation.html.twig');
    }
}
