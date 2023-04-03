<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Alcool;
use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class AppFixtures extends Fixture
{
    //php bin/console doctrine:fixtures:load --env=test
    public function load(PersistenceObjectManager $manager)
    {
        // CART
        $cart = new Cart();
        $cart->setIsValid(false);

        $manager->persist($cart);

        // USER
        $user = new User();
        $user
            ->setEmail('test@test.test')
            ->setPassword('testtest')
            ->setRoles(['ROLE_USER'])
            ->setPhoneNumber('0606060606')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setDiscount(0)
            ->setAddressFirstLine('1 rue de la rue')
            ->setAddressSecondLine('près du pré')
            ->setAddressPostal('54000')
            ->setTown('Nancy')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setIsActiv(true)
            ->setIsVerified(true)
            ->setCart($cart);

        $manager->persist($user);

        // ADDRESS
        $address = new Address();
        $address->setUser($user)
                ->setTitle('NEW')
                ->setFirstName('John')
                ->setLastName('Doe')
                ->setAddressFirstLine('2 rue du choco')
                ->setAddressSecondLine('en bas')
                ->setAddressPostal('75000')
                ->setAddressTown('Paris');

        $manager->persist($address);
        $manager->flush();
    }
}