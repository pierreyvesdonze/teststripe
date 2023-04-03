<?php

namespace App\Tests\Functional;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressTest extends WebTestCase
{

    use AuthenticationTrait;

    //php bin/phpunit --filter testCreateAddress .
    public function testCreateAddress()
    {
        $client = static::createClient();

        $user = static::getAuthUser();

        $client->request('POST', '/adresse/ajouter');

        $address = new Address;
        $address
            ->setUser($user)
            ->setLastName("Doe")
            ->setFirstName("John")
            ->setAddressFirstLine('54 rue du pâté')
            ->setAddressSecondLine('rien')
            ->setAddressPostal('33000')
            ->setAddressTown('Bordeaux');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        //$this->assertRouteSame('user_account', ['id' => 1]); 
    }

    //php bin/phpunit --filter testReadAddress .
    public function testReadAddress()
    {
        $client = static::createClient();
        $user = static::getAuthUser();

        $addressRepository = static::getContainer()->get(AddressRepository::class);
        $address = $addressRepository->findOneBy([
            'user' => $user->getId()
        ]);

        $client->request('GET', '/adresse/voir/' . $address->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('html', 'John');
    }

    //php bin/phpunit --filter testUpdateAddress .
    public function testUpdateAddress()
    {
        $client = static::createClient();
        $user = static::getAuthUser();

        $addressRepository = static::getContainer()->get(AddressRepository::class);
        $address = $addressRepository->findOneBy([
            'user' => $user->getId()
        ]);

        $client->request('POST', '/adresse/' . $address->getId() . '/modifier');

        $address
            ->setUser($user)
            ->setLastName("Doe")
            ->setFirstName("John 2")
            ->setAddressFirstLine('54 rue du pâté')
            ->setAddressSecondLine('rien')
            ->setAddressPostal('33000')
            ->setAddressTown('Bordeaux');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->followRedirect();

        $this->assertSelectorTextContains('html', "John 2");
    }
}
