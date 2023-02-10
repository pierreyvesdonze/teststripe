<?php

namespace App\Tests;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Entity\Discount;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

// php bin/phpunit .
class CartTest extends TestCase
{
    public function testCart(): void
    {
        $user = new User();
        $cart = new Cart();
        $cartLine = new CartLine();
        $cartLineToRemove = new CartLine();
        $discount = new Discount();

        $cart->addCartLine($cartLine)
             ->addCartLine($cartLineToRemove)
             ->removeCartLine($cartLineToRemove)
             ->setIsValid(true)
             ->setUser($user)
             ->setDiscount($discount);

        $this->assertEquals($cartLine, $cart->getCartLines());
        $this->assertEquals(true, $cart->isValid());
        $this->assertEquals($user, $cart->getUser());
        $this->assertEquals($discount, $cart->getDiscount());
    }
}
