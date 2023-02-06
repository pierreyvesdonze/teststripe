<?php

namespace App\Service;

use App\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscountManager extends AbstractController
{

    public function getDiscount($cart, $totalCart)
    {
        $discountAmount = (int)$cart->getDiscount()->getAmount();
        $discount = ($totalCart * $discountAmount) / 100;

        return $discount;
    }

    public function getDiscountCode(Cart $cart)
    {
        return (string)$cart->getDiscount()->getCode();
    }
}
