<?php

namespace App\Tests;

use App\Entity\Address;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

// php bin/phpunit .
class AddressTest extends TestCase
{
    public function testNewAddress(): void
    {
        $user = new User();

        $address = new Address();
        $address->setTitle('Adresse test')
                ->setFirstName('John')
                ->setLastName('Doe')
                ->setAddressFirstLine('1 rue du pont')
                ->setAddressSecondLine('à droite')
                ->setAddressPostal('88000')
                ->setAddressTown('Vosgie')
                ->setUser($user);

        $this->assertEquals('Adresse test', $address->getTitle());
        $this->assertEquals('John', $address->getFirstName());
        $this->assertEquals('Doe', $address->getLastName());
        $this->assertEquals('1 rue du pont', $address->getAddressFirstLine());
        $this->assertEquals('à droite', $address->getAddressSecondLine());
        $this->assertEquals('88000', $address->getAddressPostal());
        $this->assertEquals('Vosgie', $address->getAddressTown());
        $this->assertEquals($user, $address->getUser());
    }
}
