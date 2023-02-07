<?php

namespace App\Service;

use App\Entity\Cart;
use App\Repository\DiscountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscountManager extends AbstractController
{
    public function __construct(private DiscountRepository $discountRepository)
    {
        
    }
    public function getDiscount($userInput) 
    {
        $discount = $this->discountRepository->findOneBy([
            'code' => $userInput
        ]);

        if ($discount) {
            return $discount;
        }

        return false;
    }

    public function getDiscountAmount($cart, $totalCart)
    {
        $discountAmount = $cart->getDiscount()->getAmount();
        $discount = ($totalCart * $discountAmount) / 100;

        return $discount;
    }

    public function getDiscountCode(Cart $cart)
    {
        return (string)$cart->getDiscount()->getCode();
    }
}
